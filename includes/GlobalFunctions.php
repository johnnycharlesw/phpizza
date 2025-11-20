<?php
namespace PHPizza;
# Global functions

use PHPizza\Addons\Extension;
use PHPizza\Addons\Skin;
# Extensions: Load them, use them, see if they do anything useful.
global $loadedExtensions;
$loadedExtensions = [];

function loadExtension(string $extension){
    global $loadedExtensions;
    $loadedExtensions[] = new Extension($extension); # Load extension
}

function loadExtensions(array $extensions){
    foreach ($extensions as $extension) {
        if (is_string($extension)) {
            loadExtension($extension);
        } else {
            throw new \Exception("Extension names must be strings.");
        }
    }
}

function runExtensions(){
    global $loadedExtensions;
    foreach ($loadedExtensions as $extension) {
        $extension->activate();
    }
}

function getLoadedExtensions(): array {
    global $loadedExtensions;
    return $loadedExtensions;
}

