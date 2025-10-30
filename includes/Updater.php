<?php
namespace PHPizza;


class Updater {
    private $threshold;
    public function __construct() {
        error_log("Updater class instantiated");
        $this->threshold=file_get_contents(__DIR__ . "/.phpizza-update-threshold");
        $this->threshold=(float)$this->threshold;
        
    }

    public function get_is_available(){
        
        
        if (file_exists(__DIR__ . "/.phpizza-updater.lock")) {
            return false;
        }else{
            if ($this->threshold >= 20){
                return true;
            }
            $this->threshold++;
            $this->save_threshold_to_disk();
            return false;
            
            
        }
        
        #return !file_exists(__DIR__ . "/.phpizza-updater.lock");
        
    }

    public function save_threshold_to_disk(){
        $threshold=$this->threshold;
        $thresholdStr=(string)$threshold;
        file_put_contents(__DIR__ . "/.phpizza-update-threshold", $thresholdStr);
    }

    public function install_updates_if_available(){
        file_put_contents(__DIR__ . "/.phpizza-updater.lock", "");
        foreach (["", " origin", " upstream"] as $extraArguments) {
            system("git fetch" . $extraArguments);
        }
        system("composer update");
        unlink(__DIR__ . "/.phpizza-updater.lock"); // Remove lock file after update
        $this->threshold=0;
        $this->save_threshold_to_disk();
    }
}