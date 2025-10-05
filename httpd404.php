<?php
$title = trim($_SERVER["REQUEST_URI"],"/");
$title=parse_url($title, PHP_URL_PATH);
$_GET["title"]=$title;
$_GET["editing"]=$_GET["editing"];
include 'index.php';