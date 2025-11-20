<?php
include 'init.php';
use PHPizza\Rendering\AssetLoaderEntryPoint;

$assetLoaderEntryPoint=new AssetLoaderEntryPoint();
$assetLoaderEntryPoint->run($_GET["t"]);