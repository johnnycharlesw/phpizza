<?php
namespace PHPizza;
class PizzadownEmbedHandlerWebPage extends PizzadownEmbedHandler {
    public function render(){
        $url=trim($value);
        global $allowWebPageEmbeds;
        if (!$allowWebPageEmbeds){
            return '<!-- Embed not rendered because the site owner disabled webpage embeds -->';
        } else {
            // Check whether the target allows being framed (X-Frame-Options or CSP frame-ancestors)
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 6,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_USERAGENT => 'PHPizza-FrameCheck/1.0',
                CURLOPT_HEADER => true,
            ]);
            $headerStr = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $blocked = false;
            if ($headerStr && $httpCode >= 200 && $httpCode < 400) {
                $headers = explode("\n", $headerStr);
                foreach ($headers as $h) {
                    $h = trim($h);
                    if (stripos($h, 'x-frame-options:') === 0) {
                        $blocked = true;
                        break;
                    }
                    if (stripos($h, 'content-security-policy:') === 0 && stripos($h, 'frame-ancestors') !== false) {
                        $blocked = true;
                        break;
                    }
                }
            }

            if ($blocked) {
                // Fallback: show a link and a notice instead of an iframe
                $safeUrl = htmlspecialchars($url, ENT_QUOTES);
                return "<div class=\"embedded-webpage-blocked\">Embedded page cannot be framed (site policy). <a href=\"{$safeUrl}\" target=\"_blank\" rel=\"noopener noreferrer\">Open in new tab</a></div>";
            }

            return <<<HTML
                <iframe 
                src="{$url}"
                 class="embedded-webpage"
                ></iframe>
HTML;
                }
            }
}