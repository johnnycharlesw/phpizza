<?php
namespace PHPizza;


class Updater {
    private $threshold;
    public function __construct() {
        error_log("Updater class instantiated");
        $this->threshold=file_get_contents(__DIR__ . "/.phpizza-update-threshold");
        
    }

    public function get_is_available(){
        
        /*
        if (file_exists(__DIR__ . "/.phpizza-updater.lock")) {
            return false;
        }else{
            if ($this->threshold == 20){
                return true;
            }
            return false;
            
        }
        */
        return !file_exists(__DIR__ . "/.phpizza-updater.lock");
        
    }

    public function save_threshold_to_disk(){
        file_put_contents(__DIR__ . "/.phpizza-update-threshold",$this->threshold);
    }

    public function install_updates_if_available(){
        file_put_contents(__DIR__ . "/.phpizza-updater.lock", "");
        foreach (["", " origin", " upstream"] as $extraArguments) {
            system("git fetch" . $extraArguments);
        }
        system("composer update");
        system("rm " . __DIR__ . "/.phpizza-updater.lock");
        $this->threshold=0;
        $this->save_threshold_to_disk();
    }
}