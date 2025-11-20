<?php
namespace PHPizza;
class PizzadownEmbedHandler{
    private $embedType;
    private $value;
    public $renderedHTML;
    // This is a base class for all embed handlers, the parsing is done in Pizzadown itself
    public function __construct($embedType, $value){
        $this->embedType = $embedType;
        $this->value = $value;
    }
    public function render(){
        return $this->renderedHTML;
    }
}