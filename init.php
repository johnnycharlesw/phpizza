<?php

namespace PHPizza;
use PHPizza\Rendering\ErrorScreen;

global $isInstaller;
global $settingsDB;
global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;

// Initialize $isInstaller to false by default
$isInstaller = false;

# Load dependencies using composer autoloading
if (file_exists("vendor/autoload.php")) {
    @include "vendor/autoload.php";
} elseif (file_exists("../vendor/autoload.php")) {
    @include "../vendor/autoload.php";
} else {
    error_log("Composer autoload script not found!");

    // First, sync git submodules
    @system("git submodule update --recursive");
    // This is needed for the first run, as the submodules are not initialized by Composer.
    // It is also needed for git-clone-and-run.

    // Do NOT run composer automatically during web requests — it can block Apache/PHP.
    // Only attempt to run composer when invoked from CLI (developer convenience).
    if (php_sapi_name() === 'cli') {
        @system("composer install");
        if (file_exists("vendor/autoload.php")) {
            include 'vendor/autoload.php';
        } else {
            die('Missing dependencies: composer install did not produce vendor/autoload.php. Please run "composer install" and try again.');
        }
    } else {
        // Running under webserver — fail fast with an actionable message instead of blocking the request
        http_response_code(500);
        die('Missing dependencies. Please run "composer install" in the project root (web server cannot run composer automatically).');
    }
}

if (!dir(__DIR__ . '/node_modules')) {
    @system("npm i");
}


include 'includes/SpecialPages/specialPageClassMap.php';

$embedTypeClassMapping = [
    "youtube" => PizzadownEmbedHandlerYouTube::class,
    "mastodon" => PizzadownEmbedHandlerMastodon::class,
    'NoradSantaTracker' => PizzadownEmbedHandlerNoradSantaTracker::class,
    "facebook" => PizzadownEmbedHandlerFacebook::class,
    "webpage" => PizzadownEmbedHandlerWebPage::class,
];

function _load_config(){
    global $isInstaller, $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
    
    // Check if config.php exists FIRST - if not, we're in installer mode
    // This must be checked before including default-config.php to prevent MariaDB connection attempts
    $configExists = file_exists(__DIR__ . '/config.php');
    
    // Insert config defaults (this sets $dbType = "mariadb" which we'll override if in installer mode)
    @include __DIR__ . '/default-config.php';
    
    // If config.php doesn't exist, we're in installer mode - override defaults immediately
    if (!$configExists) {
        $isInstaller = true;
        // Immediately override database settings to SQLite to prevent any MariaDB connection attempts
        $dbServer = "localhost";
        $dbUser = "phpizza";
        $dbPassword = "";
        $dbName = "includes/Installer/phpizza_installer.sqlite3";
        $dbType = "sqlite";
    } else {
        // Config file exists, try to load it
        try {
            @include __DIR__ . '/config.php';
            // After loading config.php, check if database variables are set
            // If they're not set or invalid, we might still need installer mode
            if (!isset($dbType) || empty(trim($dbType ?? ''))) {
                // Database type not set, might need installer
                // But don't force installer mode if config exists - let it try to use the config
            }
        } catch (\Exception $e) {
            $isInstaller = true;
        }
    }
    
    // Only load additional configs if we're not in installer mode
    if (!$isInstaller) {

        // Load site-specific configuration file if it exists
        global $siteDomain;
        $siteDomain = preg_replace("/^www\./","",$_SERVER["HTTP_HOST"] ?? "phpizza.localhost");
        global $isApi;
        $isApi=false;
        if (preg_match('/^api\./', $siteDomain, $matches)) {
            $siteDomain=ltrim($siteDomain, "api.");
            $isApi=true;
        }
        @include __DIR__ . "/config.$siteDomain.php";

        // Reduce error verbosity for web requests to avoid exposing deprecation notices to visitors
        if (php_sapi_name() !== 'cli') {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
            ini_set('display_errors', '0');
        }

        // Load everything for the config
        $configdir=dir(__DIR__ . "/config.d");
        while (($file = $configdir->read()) !== false) {
            try {
                @include $file;
            } catch (\Exception $e) {
                error_log('file not found in config.d');
            }
            
        }
        $configdir->close();
    }
    if ($isInstaller) {
        include 'includes/Installer/installerEnvConfig.php';
    }
    
    // CRITICAL: After all config files are loaded, force installer mode to use SQLite
    // This must be done INSIDE the function so it has access to the global variables
    // and AFTER all config.d/ files are loaded to override any values they might set
    if (isset($isInstaller) && $isInstaller) {
        // Always use SQLite for installer mode, overriding any values from default-config.php or config.d/
        $dbServer = "localhost";
        $dbUser = "phpizza";
        $dbPassword = "";
        $dbName = "includes/Installer/phpizza_installer.sqlite3";
        $dbType = "sqlite";
    }
}
_load_config();

// Ensure we're working with global variables
global $dbServer, $dbUser, $dbPassword, $dbName, $dbType, $isInstaller;

// Final override: If in installer mode, ensure database variables are set to installer defaults
// This is a safety net in case something overrode them after _load_config()
if (isset($isInstaller) && $isInstaller) {
    // Always use SQLite for installer mode, regardless of what was set
    $dbServer = "localhost";
    $dbUser = "phpizza";
    $dbPassword = "";
    $dbName = "includes/Installer/phpizza_installer.sqlite3";
    $dbType = "sqlite";
    
    // Force update global scope to ensure all code sees the correct values
    $GLOBALS['dbServer'] = $dbServer;
    $GLOBALS['dbUser'] = $dbUser;
    $GLOBALS['dbPassword'] = $dbPassword;
    $GLOBALS['dbName'] = $dbName;
    $GLOBALS['dbType'] = $dbType;
}

// If $dbPassword wasn't provided in config.php, try loading passwd.b64
if (empty($dbPassword ?? null)) {
    $candidates = [
        __DIR__ . '/passwd.b64',
        __DIR__ . '/../passwd.b64',
        getcwd() . '/passwd.b64',
    ];
    foreach ($candidates as $file) {
        if (file_exists($file)) {
            $raw = file_get_contents($file);
            if ($raw !== false) {
                $decoded = base64_decode(trim($raw));
                if ($decoded !== false) {
                    $dbPassword = $decoded;
                    break;
                }
            }
        }
    }
}

// If still not set and we're in installer mode with SQLite, set empty password (SQLite doesn't use passwords)
if (!isset($dbPassword) && isset($isInstaller) && $isInstaller && isset($dbType) && strtolower(trim($dbType)) === 'sqlite') {
    $dbPassword = '';
}

// Only load settings from database if all required database variables are defined and valid
// Check that all variables are set, are strings, and are non-empty (password can be empty string for SQLite)
// isset() returns false for NULL, so we don't need to check !== null separately
// IMPORTANT: Skip database loading in installer mode - the installer will set up the database
$dbVarsValid = (
    !(isset($isInstaller) && $isInstaller) && // Don't load database config in installer mode
    isset($dbServer) && is_string($dbServer) && trim($dbServer) !== '' &&
    isset($dbUser) && is_string($dbUser) && trim($dbUser) !== '' &&
    isset($dbPassword) && is_string($dbPassword) && // Password can be empty string, so just check it's a string
    isset($dbName) && is_string($dbName) && trim($dbName) !== '' &&
    isset($dbType) && is_string($dbType) && trim($dbType) !== ''
);

if ($dbVarsValid) {
    // Double-check variables are still valid right before creating Database (defensive programming)
    // Re-fetch from global scope to ensure we have the latest values
    global $dbServer, $dbUser, $dbPassword, $dbName, $dbType, $isInstaller;
    
    // CRITICAL: If in installer mode, force SQLite regardless of what $dbType says
    if (isset($isInstaller) && $isInstaller) {
        $dbType = "sqlite";
        $dbServer = "localhost";
        $dbUser = "phpizza";
        $dbPassword = "";
        $dbName = "includes/Installer/phpizza_installer.sqlite3";
    }
    
    if (isset($dbType) && is_string($dbType) && trim($dbType) !== '') {
        // Load settings from the site_settings table
        try {
            $settingsdb=new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
            $settingsdb->load_config();
        } catch (\Exception $e) {
            // If database connection fails, log error but don't crash
            error_log("Failed to load configuration from database: " . $e->getMessage());
        }
    } else {
        error_log("Skipping database configuration load: dbType is invalid (" . var_export($dbType, true) . ")");
    }
}


// Optional debug logging (only emit when $debug is enabled)
if (!empty($debug)) {
    error_log("PHPizza init: dbServer=" . ($dbServer ?? 'undefined') . ", dbUser=" . ($dbUser ?? 'undefined') . ", dbName=" . ($dbName ?? 'undefined'));
}

// Activate extensions
runExtensions(); // global function

