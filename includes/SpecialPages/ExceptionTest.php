<?php
namespace PHPizza\SpecialPages;

use Override;
use PHPIzza\Exception;

class ExceptionTest extends SpecialPage
{
    #[Override]
    public function __construct($name, $title, $content)
    {
        parent::__construct($name, $title, $content);
    }

    #[Override]
    public function getContent()
    {
        throw new Exception("Error Processing Request", 1);
        
        return "";
    }
}
