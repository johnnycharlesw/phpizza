<?php
namespace PHPizza\Rendering;
@include '../../init.php';


class AssetLoaderEntryPoint{
    private $assetLoader;

    public function __construct(){
        $this->assetLoader=new AssetLoader();
    }
    
    public function run(string $type){
        // Disable PHP error display, as it breaks CSS because it comes out as HTML
        ini_set("display_errors",false);
        error_reporting(0);

        // Custom error handler
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $error = "[ERROR {$errno}] {$errstr} in {$errfile} on line {$errline}";
            echo "/* {$error} */";
            return true; // Prevent default error handling
        });

        // Load assets using AssetLoader
        
        $this->assetLoader->fromQuery($type);
    }
}