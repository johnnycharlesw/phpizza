<?php
namespace PHPizza\SpecialPages;
class DestroySessionToken extends SpecialPage {
    public function __construct($name, $title, $content) {
        $title = "Destroy Session Token (API USE ONLY!)";   
        parent::construct($name, $title, $content);
    }

    public function getContent()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        header("Connection: close");
        return <<<MD
# Session token invalidated

Your app has now fully logged off. For security reasons, you may not make any further requests on this TCP connection.
MD;
    }
}