<?php
namespace PHPizza;
use PHPizza\IllegalStateException;
class PizzadownEmbedHandlerYouTubeShorts extends PizzadownEmbedHandler {
    private $configdb;
    public function __construct($value) {
        throw new IllegalStateException("");
        parent::__construct('youtubeshorts', $value);
        // global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        // $this->configdb = new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        // $this->configdb->register_key("allowGoogleOwnedServiceEmbeds", true);
    }
    public function render() {
        // if (!($allowGoogleOwnedServiceEmbeds ?? false)){
        //     return '<!-- Embed not rendered because the site owner opted out of Google-owned service embeds -->';
        // }

        $doggyProtectsPregnantMomma = "s2M2U-N_sWc";

        $videoID=htmlspecialchars($this->value ?? $doggyProtectsPregnantMomma,ENT_QUOTES);

        return <<<HTML
        <iframe
        class="embedded-video embedded-shortform-video"
        src="https://youtube.com/embed/{$videoID}?loop=1"
        title="YouTube video player" 
        allow="autoplay; clipboard-write self https://www.youtube.com; encrypted-media; picture-in-picture; web-share"  
        allowfullscreen
        ></iframe>
HTML;
    }
}