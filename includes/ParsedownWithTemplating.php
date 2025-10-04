<?php
namespace PHPizza;

class ParsedownWithTemplating extends \Parsedown {
    public function __construct() {
        parent::__construct();
    }

    public function text($markdown, array $vars = [])
    {
        $parsed=parent::text($markdown);
        foreach ($vars as $key => $value) {
            $parsed=str_replace("{{$key}}",$value,$parsed);
        }
        return $parsed;
    }
}