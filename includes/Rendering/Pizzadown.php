<?php
namespace PHPizza\Rendering;

class Pizzadown extends \Parsedown{

    // Whether to load templates from DB
    protected $dbTemplates = false;

    // We register the Embed block in the constructor so we don't overwrite
    // the parent's BlockTypes and accidentally disable other block markers.

    public function __construct($dbTemplates = false){
        $this->dbTemplates=$dbTemplates;
        // Safely add our Embed block to the existing BlockTypes so we don't break other block markers
        if (!isset($this->BlockTypes) || !is_array($this->BlockTypes)) {
            $this->BlockTypes = [];
        }
        if (!isset($this->BlockTypes['!']) || !is_array($this->BlockTypes['!'])) {
            $this->BlockTypes['!'] = [];
        }
        if (!in_array('Embed', $this->BlockTypes['!'], true)) {
            $this->BlockTypes['!'][] = 'Embed';
        }
    }

    // Add classes to unordered lists based on marker type: '-' => list-dash, '*' => list-disc, '+' => list-plus
    public function blockList($Line, $CurrentBlock = null){
        // Let Parsedown build the list structure first
        $Block = parent::blockList($Line, $CurrentBlock);
        if (!$Block) {
            return null;
        }

        // Only apply to unordered lists
        if (isset($Block['element']['name']) && $Block['element']['name'] === 'ul') {
            $markerClass = null;
            if (isset($Line['text'][0])) {
                $first = $Line['text'][0];
                if ($first === '-') {
                    $markerClass = 'list-dash';
                } elseif ($first === '*') {
                    $markerClass = 'list-disc';
                } elseif ($first === '+') {
                    $markerClass = 'list-plus';
                }
            }

            if ($markerClass !== null) {
                if (!isset($Block['element']['attributes'])) {
                    $Block['element']['attributes'] = [];
                }
                if (isset($Block['element']['attributes']['class']) && $Block['element']['attributes']['class'] !== '') {
                    $Block['element']['attributes']['class'] .= ' ' . $markerClass;
                } else {
                    $Block['element']['attributes']['class'] = $markerClass;
                }
            }
        }

        return $Block;
    }

    // Parsedown will call blockEmbed with ($Line, $CurrentBlock)
    public function blockEmbed($Line, $CurrentBlock = null){
        if (preg_match('/^!(\w+)\[(.+)\]$/', $Line['text'], $matches)){
                error_log("Pizzadown::blockEmbed called with: " . $Line['text']);
            $embedType = strtolower($matches[1]);
            $value = trim($matches[2]);
            $renderedHTML = $this->renderEmbed($embedType, $value);
            // Add a visible marker in the output so the page source shows the embed was processed
            $renderedHTML = "<!--PizzadownEmbed:{$embedType}-->" . $renderedHTML;

            // Return a block compatible with Parsedown's element() renderer
            $Block = [
                'element' => [
                    'name' => 'div',
                    'rawHtml' => $renderedHTML,
                    'allowRawHtmlInSafeMode' => true,
                ],
            ];

            return $Block;
        }

        return null;
    }

    public function blockTemplate($Line, $CurrentBlock = null){
        if (preg_match('/\\{\\{([^}]*)\\}\\}/i', $Line['text'], $matches)){
            $csv = $matches[1];
            // Only treat as a template include if it looks like one (has a pipe, a path, a dot, or Template: prefix)
            if (strpos($csv, '|') === false && strpos($csv, '/') === false && strpos($csv, '.') === false && stripos($csv, 'template:') === false) {
                return null;
            }

            $params_ = str_getcsv($csv,"|");
            $name = $params_[0];
            $params = array_merge([
                "name" => $name,
            ], array_slice($params_,1,null,$preserve_keys=true));
            $renderedHTML = $this->renderTemplate($params);

            return [
                'element' => [
                    'name' => 'div',
                    'rawHtml' => $renderedHTML,
                    'allowRawHtmlInSafeMode' => true,
                ],
            ];
        }
        return null;
    }

    public function renderTemplate($params){
        $name=$params['name'];
        if ($this->dbTemplates){
            global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
            $pagedb=new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);    
            $template=$pagedb->getPage("Template:$name");
            return $this->templateText($template['content']);
        }else{
            // Only attempt to read files that exist to avoid warnings; otherwise return empty
            if (file_exists($name) && is_readable($name)){
                return $this->templateText(file_get_contents($name), $params);
            }
            return '';
        }
    }

    public function templateText($text, $params = []){
        
        $parsed=$this->text($text);

        foreach ($params as $key => $value) {
            // Ensure we don't pass null to trim() (PHP 8.3 deprecation).
            // If value is not a string, cast to string; if null, become empty string.
            if (!is_string($value)) {
                if ($value === null) {
                    $value = '';
                } else {
                    $value = (string)$value;
                }
            }
            $parsed = str_replace('{{{' . $key . '}}}', trim($value), $parsed);
        }

        return $parsed;
    }

    // No blockParagraph override — let Parsedown handle paragraphs and call blockEmbed when appropriate.
    
    public function renderEmbed($type, $value){
        global $embedTypeClassMapping;
        if (array_search($type,array_keys($embedTypeClassMapping))) {
            $embedHandlerClass = $embedTypeClassMapping[$type];
            $embedHandler = new $embedHandlerClass($value);
            return $embedHandler->render();
        }
        switch ($type){
            case 'youtube':

                if (!(isset($allowGoogleOwnedServiceEmbeds) ? $allowGoogleOwnedServiceEmbeds : false)){
                    return '<!-- Embed not rendered because the site owner opted out of Google-owned service embeds -->';
                }
                $videoID=htmlspecialchars($value,ENT_QUOTES);
                return <<<HTML
                    <iframe
                        class="embedded-video"
                        src="https://youtube.com/embed/{$videoID}"
                        allowfullscreen
                    ></iframe>
                HTML;
            case 'bluesky':
                $siteLocation=htmlspecialchars($value,ENT_QUOTES);
                return <<<HTML
                    <iframe
                        class="embedded-social bluesky-embed"
                        src="https://bsky.app/{$siteLocation}"
                        loading="lazy"
                        allow="clipboard-write"
                    ></iframe>
                HTML;
            case 'mastodon':

                # Handle, well, the handle

                $handle=trim($value);
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
            case 'facebook':
                # Placeholder for facebook intergration
            case 'twitter':
                return "<!-- Embed not rendered to avoid Musk drama -->";
            case 'webpage':
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
                        CURLOPT_USERAGENT => 'PHPizzaCMS-FrameCheck/1.0',
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
