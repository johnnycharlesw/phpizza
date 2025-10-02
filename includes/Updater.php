<?php
namespace PHPizza;

class Updater {
    public function __construct() {
        error_log("Updater class instantiated");
    }

    public function install_updates_if_available(){
        foreach (["", " origin", " upstream"] as $extraArguments) {
            system("git fetch" . $extraArguments);
        }
        system("composer update");
    }
}