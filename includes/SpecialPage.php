<?php
namespace PHPizza;
class SpecialPage {
    private $name;
    private $title;
    private $content;

    public function __construct($name, $title, $content) {
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
    }

    public function getName() {
        return $this->name;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getSpecialPageData(){
        return [
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'content' => $this->getContent()
        ];
    }
}