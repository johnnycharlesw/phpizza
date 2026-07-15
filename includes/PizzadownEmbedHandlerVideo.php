<?php
namespace PHPizza;
class PizzadownEmbedHandlerVideo extends PizzadownEmbedHandler {
    // This is a class where I ported the YouTube embed handler to OOP
    public function __construct(string $value) {
        parent::__construct('video', $value);
        $this->value=$value;
    }
    public function render() {
        $videoID=htmlspecialchars($this->value,ENT_QUOTES);

        return <<<HTML
        <div style="text-align:center"> 
            <video id="video" width="420">
                <source src="mov_bbb.mp4" type="video/mp4">
                Your browser does not support HTML video.
            </video>
            <br>
            <div id="controls">
            <button onclick="playPause()"><img src="/node_modules/feather-icons/dist/icons/play.svg" class="icon"></img></button> 
            </div>
        </div> 
HTML;
    }
}