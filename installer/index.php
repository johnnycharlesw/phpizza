<?php
namespace PHPizza\Installer;
$isInstaller=true;
include "../init.php";

# Redirect all HTTP traffic to HTTPS because this installer handles sensitive data
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit();
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

if (isset($_POST)) {
    $_SESSION["data"] = array_merge_recursive(
        $_SESSION["data"],
        $_POST
    );
}

use PHPizza\PageRenderer;

$renderer=new PageRenderer();
$sceneName= (isset($_POST["scene"])) ? $_POST["scene"] : ((isset($_GET["scene"])) ? $_GET["scene"] : "user-greeting-scene");
$scenePage_=include "scenes/" . $sceneName . ".php";
$scenePage=$scenePage_["html"];
$sceneTitle=$scenePage_["title"];
$sceneSpecificCSS=file_get_contents("scenes/" . $sceneName . ".css");
$installerCSS=file_get_contents("style.css");
$sceneBodyHTML= <<<HTML
<style>
    {$installerCSS}
    {$sceneSpecificCSS}
</style>
{$scenePage}
HTML;

echo $renderer->get_html_page(
    "PHPizza installer",
    $sceneTitle,
    "This is an installer page",
    [],
    $scenePage,
    "en"
);