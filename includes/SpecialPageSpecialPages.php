<?php
namespace PHPizza;
class SpecialPageSpecialPages extends SpecialPage {
    public function __construct() {
        // Pass an empty content string and override getContent() to generate dynamic content
        parent::__construct("SpecialPages", "View a list of all special pages", "");
    }

    // Use camelCase to match the base class API
    public function getContent() {
        global $specialPageClassMap;
        $content = "<h1>Special Pages</h1>\n";
        $content .= "<ul>\n";

        $map = is_array($specialPageClassMap) ? $specialPageClassMap : [];
        foreach ($map as $pageName => $className) {
            $url = "/index.php?title=PHPizza:" . rawurlencode($pageName);
            $safeName = htmlspecialchars($pageName, ENT_QUOTES | ENT_SUBSTITUTE);
            $content .= "<li><a href=\"{$url}\">{$safeName}</a></li>\n";
        }
        $content .= "</ul>\n";
        return $content;
    }
}