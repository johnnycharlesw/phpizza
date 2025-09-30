<?php
namespace PHPizza;

class Skin extends Addon
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'skin');
    }

    public function get_skin_class() {
        $name=$this->name;
        $name=strtolower($name);
        return "phpizza-skin-$name";
    }

    public function get_component(string $type) {
        $name=$this->name;
        $parsedown = new \Parsedown();
        $markdown = file_get_contents($this->assetPath("parts/$type.md"));
        $parsed=$parsedown->text($markdown);
        return $parsed;
    }

    public function get_header(){
        return $this->get_component('header');
    }

    public function get_sidebar(){
        return $this->get_component('sidebar');
    }

    public function get_footer(){
        return $this->get_component('footer');
    }
}
