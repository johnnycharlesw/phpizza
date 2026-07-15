<?php
namespace PHPizza;

class PizzadownEmbedHandlerFacebook extends PizzadownEmbedHandler {
    private $value;
    private $configdb;
    public function __construct($value) {
        parent::__construct('facebook', $value);
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->configdb = new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->configdb->register_key('allowFacebookEmbeds', false);
    }
    public function render(){
        global $allowFacebookEmbeds;
        if (!$allowFacebookEmbeds) {
            return "<!-- Embed not rendered because Facebook embeds are disabled -->";
        }
        $parsedValue=explode(",",$this->value);
        $handle=$parsedValue[0];
        $postID=$parsedValue[1];
        $url="https://www.facebook.com/plugins/page.php?href=https://www.facebook.com/{$handle}/posts/{$postID}"; //The iframe method, not the oEmbed method.php?href=https://www.facebook.com/{$handle}/posts/{$postID}"; //The iframe method, not the oEmbed method
        return <<<HTML
        <iframe
        src="{$url}"
        class="embedded-social facebook-embed"
        loading="lazy"
        allow="clipboard-write"
        ></iframe>
HTML;
    }
}