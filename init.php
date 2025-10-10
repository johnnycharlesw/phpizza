<?php
global $isInstaller;
global $settingsDB;

# Load dependencies using composer autoloading
if (file_exists("vendor/autoload.php")) {
    @include "vendor/autoload.php";
} elseif (file_exists("../vendor/autoload.php")) {
    @include "../vendor/autoload.php";
} else {
    error_log("Composer autoload script not found!");
    
    system("composer install");
    if (file_exists("vendor/autoload.php")) {
        include 'vendor/autoload.php';
    }else{
        die('Missing dependencies. When it was automatically run here, it did not generate the autoload file. Please run "composer install" manually.');
    }
}

if (!isset($isInstaller)) {
    // Load configuration file
    @include __DIR__ . '/config.php';

    // Load everything for the config
    $configdir=dir(__DIR__ . "/config.d");
    while (($file = $configdir->read()) !== false) {
        try {
            @include $file;
        } catch (Exception $e) {
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

// Optional debug logging
if (!empty($debug)) {
    error_log("PHPizza init: dbServer={$dbServer}, dbUser={$dbUser}, dbName={$dbName}");
}
