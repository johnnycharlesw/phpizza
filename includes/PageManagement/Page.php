<?php
namespace PHPizza\PageManagement;
class Page {
    public function __construct(Type $var = null) {
        $this->var = $var;
    }
}