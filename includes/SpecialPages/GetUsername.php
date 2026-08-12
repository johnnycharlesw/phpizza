<?php
namespace PHPizza\SpecialPages;

use Override;

class GetUsername extends SpecialPage{
  
    public function __construct($name, $title, $content) 
    {
        return parent::__construct('Username', $title, $content);
    }

    public function getContent()
    {
        global $isApi;
        $authorized = $isApi || preg_match('/phpizza-desktop/', $_SERVER['HTTP_USER_AGENT']);
        if (!$authorized) {
            # code...
        }
        global $guestUsername;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            return $guestUsername;
        }
        $username = $_SESSION['username'];
        return <<<HTML
        {$username}
        HTML;
    }
}
