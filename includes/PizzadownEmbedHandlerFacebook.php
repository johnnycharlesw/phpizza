<?php
namespace PHPizza;

class PizzadownEmbedHandlerFacebook extends PizzadownEmbedHandler {
    public function render(){
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