<?php
namespace PHPizza;
@include '../init.php';


class AssetLoaderEntryPoint{
    private $assetLoader;

    public function __construct(){
        $this->assetLoader=new AssetLoader();
    }
    
    public function run(string $type){
        // Disable PHP error display, as it breaks CSS because it comes out as HTML
        ini_set("display_errors",false);
        error_reporting(0);

        // Load assets using AssetLoader
        $skinName=$_GET["skin"] ? $_GET["skin"] : ($_POST["skin"] ? $_POST["skin"] : "PHPizza");
        $skin=new Skin($skinName);

        $this->assetLoader->fromQuery($type);
    }
}