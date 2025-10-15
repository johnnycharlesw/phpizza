<?php
namespace PHPizza\Installer;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Register shutdown handler to show fatal errors when running under a webserver
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err !== null) {
        // Output a minimal HTML fragment so it's visible in browser
        echo "<pre style=\"color:maroon;background:#fff8f8;padding:12px;border:1px solid #f5c2c2;\">";
        echo "Fatal error detected:\n";
        echo htmlspecialchars(var_export($err, true));
        echo "\n\nServer vars:\n";
        echo htmlspecialchars(json_encode(array_intersect_key($_SERVER, array_flip(['REQUEST_METHOD','REQUEST_URI','REMOTE_ADDR','HTTP_HOST','HTTPS'])), JSON_PRETTY_PRINT));
        echo "</pre>";
    }
});

$isInstaller=true;
include __DIR__ . "/../init.php";

// Redirect all HTTP traffic to HTTPS because this installer handles sensitive data
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    // Allow CLI runs and local requests on 127.0.0.1 to proceed without HTTPS
    if (!(php_sapi_name() === "cli" || (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === "127.0.0.1"))) {
        if (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirect);
        }
        exit();
    }
}


# Begin installer flow

session_start();
include 'vendorScenes.php';

if (!isset($data)) {
    $data=[];
}

$beginningScenes = [
    "user-greeting-scene",
    "license-scene",
    "site-info-scene",
    "restore-backup-from-old-server-scene",
];

if (!isset($vendorScenes)) {
    $vendorScenes = []; # If the vendor did not inject any custom scenes, do not insert any vendor scenes.
}

$endScenes = [
    "install-phpizza-scene",
    "summary",
    "load-phpizza-scene",
];

$scenes = array_merge(
    $beginningScenes,
    $vendorScenes,
    $endScenes
);

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [];
}

if (isset($_POST)) {
    $_SESSION["data"] = array_merge_recursive(
        $_SESSION["data"],
        $_POST
    );
}

use PHPizza\PageRenderer;

$renderer=new PageRenderer();
$sceneName= (isset($_POST["scene"])) ? $_POST["scene"] : ((isset($_GET["scene"])) ? $_GET["scene"] : "user-greeting-scene");
$sceneFile = __DIR__ . "/scenes/" . $sceneName . ".php";
if (!file_exists($sceneFile)) {
    http_response_code(404);
    $scenePage = "<h1>Scene not found</h1><p>Installer scene '" . htmlspecialchars($sceneName) . "' is missing.</p>";
    $sceneTitle = "Scene missing";
    $sceneSpecificCSS = '';
} else {
    $scenePage_ = include $sceneFile;
    if (is_array($scenePage_)){
        $scenePage = isset($scenePage_["html"]) ? $scenePage_["html"] : '';
        $sceneTitle = isset($scenePage_["title"]) ? $scenePage_["title"] : '';
    } else {
        $scenePage = (string)$scenePage_;
        $sceneTitle = '';
    }
    $sceneCssFile = __DIR__ . "/scenes/" . $sceneName . ".css";
    $sceneSpecificCSS = file_exists($sceneCssFile) ? file_get_contents($sceneCssFile) : '';
}

$installerCssFile = __DIR__ . "/style.css";
$installerCSS = file_exists($installerCssFile) ? file_get_contents($installerCssFile) : '';
$sceneBodyHTML= <<<HTML
<style>
    {$installerCSS}
    {$sceneSpecificCSS}
</style>
{$scenePage}
HTML;

$skinName="PHPizza";

echo $renderer->get_html_page(
    "PHPizza installer",
    $sceneTitle,
    "This is an installer page",
    [],
    $sceneBodyHTML,
    "en",
    true
);