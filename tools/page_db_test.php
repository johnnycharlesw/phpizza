<?php
require __DIR__ . '/../init.php';
$pg = new PHPizza\PageDatabase($dbServer,$dbUser,$dbPassword,$dbName,$dbType);
$map = $GLOBALS['specialPageClassMap'] ?? null;
var_export($map);
