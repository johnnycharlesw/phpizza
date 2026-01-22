<?php

namespace PHPizza;
use PHPizza\Rendering\ErrorScreen;

global $isInstaller;
global $settingsDB;

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
    // I am not actually going to support Twitter
];

// Insert config defaults
@include __DIR__ . '/default-config.php'; // For later.
if (!isset($isInstaller)) {
    // Load global configuration file
    @include __DIR__ . '/config.php';

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


// If $dbPassword wasn't provided in config.php, try loading passwd.b64
if (empty($dbPassword)) {
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


// Load settings from the site_settings table
$settingsdb=new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
$settingsdb->load_config();


// Optional debug logging (only emit when $debug is enabled)
if (!empty($debug)) {
    error_log("PHPizza init: dbServer={$dbServer}, dbUser={$dbUser}, dbName={$dbName}");
}

// Activate extensions
runExtensions(); // global function

