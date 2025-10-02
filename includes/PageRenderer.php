<?php
namespace PHPizza;

// Shim for PSR-4 autoloading: the class is defined in PageRendering.php
// and historically the file was named differently. Require it so Composer
// will find the class when it looks for \PHPizza\PageRenderer.
require_once __DIR__ . '/PageRendering.php';
