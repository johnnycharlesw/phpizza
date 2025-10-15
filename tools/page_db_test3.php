<?php
require __DIR__ . '/../init.php';
$pg = new PHPizza\PageDatabase($dbServer,$dbUser,$dbPassword,$dbName,$dbType);
var_export($pg->getPage('PHPizza:UserLogin'));
