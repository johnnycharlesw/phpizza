<?php
namespace PHPizza\Updates;


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
        $git_buf="";
        foreach (["", " origin", " upstream"] as $extraArguments) {
            $git_buf .= system("git fetch" . $extraArguments);
        }
        system("composer update");
        if ($git_buf !== "") {
            # Output occured, there is probably an update
            system("git merge");
        }
        unlink(__DIR__ . "/.phpizza-updater.lock"); // Remove lock file after update
        $this->threshold=0;
        $this->save_threshold_to_disk();
    }
}