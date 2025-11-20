<?php
namespace PHPizza;
class PizzadownEmbedHandlerYouTube extends PizzadownEmbedHandler {
    // This is a class where I ported the YouTUbe embed handler to OOP
    public function __construct($value) {
        parent::__construct('youtube', $value);
    }
    public function render() {
        if (!(isset($allowGoogleOwnedServiceEmbeds) ? $allowGoogleOwnedServiceEmbeds : false)){
            return '<!-- Embed not rendered because the site owner opted out of Google-owned service embeds -->';
        }
        $videoID=htmlspecialchars($this->value,ENT_QUOTES);
        return <<<HTML
        <iframe
        class="embedded-video"
        src="https://youtube.com/embed/{$videoID}"
        allowfullscreen
        ></iframe>
HTML;
    }
}