<?php
namespace PHPizza;

class AssetLoaderEntryPoint{
    private $assetLoader;

    public function __construct(){
        $this->assetLoader=new AssetLoader();
    }
    
    public function run(string $type){
        include 'init.php';

        $skinName=$_GET["skin"] ? $_GET["skin"] : ($_POST["skin"] ? $_POST["skin"] : "PHPizza");
        $skin=new Skin($skinName);

        $this->assetLoader->fromQuery($type);
    }
}