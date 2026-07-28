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
        global $siteDomain;
        return <<<HTML
        <iframe src="//{$siteDomain}/index.php?title=VideoRenderer.php&v={$videoID}" class="embedded-video"></iframe>
HTML;
    }
}