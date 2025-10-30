<?php
namespace PHPizza;
class Localizer {
    private $language;
    private $translations;

    public function __construct($language, $translations = []) {
        $this->language = $language;
        $this->translations = $translations;
    }

    public function translate($key) {
        if (isset($this->translations[$this->language][$key])) {
            return $this->translations[$this->language][$key];
        }
        return $key; // Fallback to key if translation not found
    }

    public function getTemplateMD($template){
        global $isInstaller;
        if (isset($isInstaller) && $isInstaller) {
            return file_get_contents(__DIR__ . "/../messages/installer/" . $template . ".md");
        }
        return file_get_contents(__DIR__ . "/../messages/cms/" . $this->language . "/" . $template . ".md");
    }
}