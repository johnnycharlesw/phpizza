<?php
namespace PHPizza;
class PizzadownEmbedHandlerYouTube extends PizzadownEmbedHandler {
    // This is a class where I ported the YouTube embed handler to OOP
    private $configdb;
    private $value;
    public function __construct(string $value) {
        parent::__construct('youtube', $value);
        $this->value=$value;
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->configdb = new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->configdb->register_key("allowGoogleOwnedServiceEmbeds", true);
    }
    public function render() {
        global $allowGoogleOwnedServiceEmbeds;
        if (!$allowGoogleOwnedServiceEmbeds){
            return '<!-- Embed not rendered because the site owner opted out of Google-owned service embeds -->';
        }

        $catsSneakIntoASuitcase = "MUOWbKh8GhM";

        $videoID=htmlspecialchars($this->value,ENT_QUOTES) ?? $catsSneakIntoASuitcase;

        return <<<HTML
        <iframe
        class="embedded-video"
        src="https://youtube.com/embed/{$videoID}"
        title="YouTube video player" 
        allow="autoplay; clipboard-write self https://www.youtube.com; encrypted-media; picture-in-picture; web-share"  
        allowfullscreen
        ></iframe>
HTML;
    }
}