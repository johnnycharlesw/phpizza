<?php
// Load initialization (config, vendor autoload)
require_once __DIR__ . '/init.php';

// Determine SAPI and pick the appropriate entrypoint
$sapi = PHP_SAPI;

$cliSapis = ['cli'];
$webSapis = ['apache', 'apache2handler', 'cgi-fcgi', 'fpm-fcgi', 'cli-server', 'litespeed'];

$isClientAPI = (isset($_GET["api_mode"])) ? $_GET["api_mode"] : false ;

$entry = null;

// If debug is enabled in config, log the SAPI for troubleshooting
if (!empty($debug)) {
    error_log('PHPizza startup: PHP_SAPI=' . $sapi);
}


try {
    if (in_array($sapi, $cliSapis, true)) {
        $entry = new \PHPizza\CLIEntryPoint();
    } elseif (in_array($sapi, $webSapis, true) || ($isPyServer && isset($isPyServer))) {
        if ($isClientAPI) {
            $entry = new \PHPizza\APIEntryPoint();
        }else {
            $entry = new \PHPizza\BrowserEntryPoint();
        }
    } elseif ($sapi === 'embed') {
        // This is a CMS. It is not designed to be embedded into desktop apps.
        $errorScreen = new \PHPizza\ErrorScreen("This is a CMS, not an app framework. Use this on your website, not a desktop app.");
        $errorScreen->render($sitename);
        exit(1);
    } else {
        // Unsupported or dangerous SAPI — behave differently in debug vs production
        if (!empty($debug)) {
            $errorScreen = new \PHPizza\ErrorScreen("This is a CMS, not an LOP/ROP chain. Use this on your website, not a hacking tool.");
            http_response_code(500);
            $errorScreen->render($sitename);
            exit(1);
        }

        // Production: hide details, return 404 and log incident
        http_response_code(500);
        error_log(sprintf("Unsupported SAPI detected: %s from %s", $sapi, $_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        echo sprintf("Unsupported SAPI detected: %s from %s", $sapi, $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        exit(1);
    }

    // Run the selected entrypoint
    if ($entry !== null) {
        $entry->run();
    }

} catch (\Throwable $e) {
    $message='Fatal error: ' . $e->getMessage();
    error_log($message);
    $err = new \PHPizza\ErrorScreen('Internal server error\n' . $message);
    $err->render($sitename);
    exit(1);
}
