<?php
namespace PHPizza\Rendering;

class Tracker {
    private $html;
    public function __construct(string $html) {
        $this->html=$html;
    }

    public function canTrackThisUser(string $username) {
        $dnt_enabled= $_SERVER["HTTP_DNT"] ?? true;
        global $guestUsername;
        $isGuest = $username === $guestUsername;
        return $dnt_enabled && !$isGuest;
    }
    
}