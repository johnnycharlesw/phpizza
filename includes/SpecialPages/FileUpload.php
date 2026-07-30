<?php

use PHPizza\SpecialPages\SpecialPage;

class FileUpload extends SpecialPage {
    #[Override]
    public function __construct($name, $title, $content)
    {
        return parent::__construct($name, $title, $content);
    }

    #[Override]
    public function getContent()
    {
        
    }
}