<?php
namespace PHPizza\Addons;

class Extension extends Addon
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'extension');
    }

    public function activate() {
        if ($this->manifest["usesComposer"] ?? true) {
            include $this->assetPath('vendor/autoload.php');
        }
        include $this->assetPath('extension.php');
        // Hide cookies
        $_COOKIE = ["PHPSESSID" => $_COOKIE["PHPSESSID"]];
    }
    
    public function deactivate() {
        include $this->assetPath('cleanup.php');
        // Hide cookies
        $_COOKIE = ["PHPSESSID" => $_COOKIE["PHPSESSID"]];
    }

    public function __destruct() {
        // Cleanup
        $this->deactivate();
    }
}

#a;