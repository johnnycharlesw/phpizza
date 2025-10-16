<?php
namespace PHPizza;

class Extension extends Addon
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'extension');
    }

    public function activate() {
        include $this->assetPath('extension.php');
    }
}

#a;