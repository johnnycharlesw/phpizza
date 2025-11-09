<?php
namespace PHPizza;
class PizzadownEmbedHandlerMastodon extends PizzadownEmbedHandler {
    public function render(){
            # Handle, well, the handle

            $handle=trim($this->value);
            if (preg_match('/^@?([a-zA-Z0-9_]+)@([a-zA-Z0-9_\.\-]+)$/',$handle,$matches)) {
                # Handle following official Fediverse syntax
                $username=$matches[1];
                $domain=$matches[2];
            } 
            elseif (preg_match('/^@([a-zA-Z0-9_]+)$/',$handle,$matches)){
                # Handle Facebook-like syntax (default server: mastodon.social)
                $username=$matches[1];
                $domain='mastodon.social';
            }
            else {
                return '<!-- Embed not rendered because of invalid Mastodon handle -->';
            }

            # Verify the server really is running Mastodon or a compatible platform
            if (!$this->verifyMastodonServerViaNodeInfo($domain)) {
                return '<!-- Embed not rendered because the server specified does not appear to actually be a Mastodon server. Nice try, though. -->';
            }

            $url="https://{$domain}/@{$username}";

            # Generate the iframe

            return <<<HTML
                <iframe
                    src="{$url}"
                    class="embedded-social mastodon-embed"
                    loading="lazy"
                    allow="clipboard-write"
                ></iframe>
HTML;
    } 
    
    public function verifyMastodonServerViaNodeInfo(string $domain){
        $domain=strtolower(trim($domain));


        if (!preg_match('/^[a-z0-9.-]+$/i', $domain)) {
            return false; # If it isn't a valid website, it has no chance of being a Mastodon server
        }

        $nodeInfoDiscoveryUrl="https://{$domain}/.well-known/nodeinfo";

        $discovery=$this->httpGetJson($nodeInfoDiscoveryUrl); # retrive NodeInfo

        if (!$discovery || !isset($discovery['links']) || !is_array($discovery['links'])){
            return false; # Really, do not try to interfere with this verification function, it is here to protect clients from fake Mastodon servers and is needed because of Mastodon's federated architecture
        }

        $nodeInfoUrl=null;
        foreach ($discovery['links'] as $link) {
            if (isset($link['rel'], $link['href']) && is_string($link['href'])) {
                $nodeInfoUrl=$link['href'];
                break;
            }
        }

        if (!$nodeInfoUrl) {
            return false; # nodeinfo files should not be tampered with like that
        }


        $nodeinfoData=$this->httpGetJson($nodeInfoUrl);
        if (!$nodeinfoData || !isset($nodeinfoData['software']['name'])){
            return false; # nodeinfo files really should not be made like that
        }

        return strtolower($nodeinfoData['software']['name']) === 'mastodon';
    }

    public function httpGetJson(string $url){
        $curl=curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'PHPizzaCMS-NodeInfo-Checker/1.0',
        ]);

        $response=curl_exec($curl);
        $code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if (!$response || $code < 200 || $code >= 300){
            return null; # In that scenario, no JSON was found or something like that
        }

        $data=json_decode($response,true);
        return is_array($data) ? $data : null;
    }
}