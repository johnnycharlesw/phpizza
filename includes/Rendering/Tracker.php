<?php
namespace PHPizza\Rendering;

class Tracker {
    private $html;
    public function __construct(string $html) {
        if ($this->canTrackThisUser($_SESSION["username"])) {
            $this->html=$html;
        } else {
            $this->html=<<<HTML
<!--
We would insert an analytics script here, but it looks like you have Privacy Badger installed or another privacy protection system installed.
This message was inserted by PHPizza.
The new 2026.9.26 update makes sites comply with Do Not Track and Global Privacy Control signals, it was pushed to everyone for your protection.
        -->
HTML;
        }
    }

    public function canTrackThisUser(?string $username) {
        $dnt_enabled= $_SERVER["HTTP_DNT"] ?? true;
        $gpc_enabled=$_SERVER["HTTP_SEC_GPC"] ?? false;
        $cannot_track = $dnt_enabled || $gpc_enabled;
        $can_track = !$cannot_track;

        global $guestUsername;
        $isGuestAccount = $username === $guestUsername;
        $isInvalidContext = $username == null;
        $isGuest = $isGuestAccount || $isInvalidContext;

        return $can_track && !$isGuest;
    }
    
}