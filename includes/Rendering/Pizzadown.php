<?php
namespace PHPizza\Rendering;

use Override;
use PHPizza\PageManagement\PageDatabase;

class Pizzadown extends \Parsedown{

    // Whether to load templates from DB
    protected $dbTemplates = false;

    private array $emoticonMap = [
        // Grinning face
        ':)' => '😀',
        ':]' => '😀',
        ':>' => '😀',
        '8)' => '😀',
        ':}' => '😀',

        // Smiling with a tear
        ":')" => '🥲',

        // Slightly smiling
        ':-)' => '🙂',
        ':-]' => '🙂',
        ':->' => '🙂',
        '8-)' => '🙂',
        ':-}' => '🙂',

        // ROFL
        'x-D' => '🤣',
        'xD' => '🤣',
        'X-D' => '🤣',
        "XD" => '🤣',
        'rofl' => '🤣',
        "ROFL" => '🤣',

        // Grinning with glasses
        ':D' => '😎',
        ':-D' => '😎',

        // Kissy face
        ':*' => '😘',
        ':-*' => '💏',

        // Angel
        'O:-)' => '😇',
        'O:)' => '😇',
        'O:-3' => '😇',
        'O:3' => '😇',

        // Crying emojis
        ':(' => '😢',
        ':c' => '😢',
        ':-(' => '😭',
        ':-c' => '😭',
        ':=(' => '😭',
        
        // Angry emojis
        '>:(' => '😡',
        '~!@#$%' => '🤬'
    ];

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
        $this->InlineTypes[':'][] = 'Emoticon';
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

    protected function loadPhemojiAssets()
    {
        $map = [];
        $files = glob($_SERVER['DOCUMENT_ROOT'] . '/assets/phemoji/assets/72x72/*.png');

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $codepoints = explode('-', $name);

            $sequence = '';

            foreach ($codepoints as $codepoint) {
                $sequence .= mb_chr(hexdec($codepoint), 'UTF-8');
            }

            $map[$sequence] = '/assets/72x72/'. basename($file);
        }

        uksort($map, function ($a, $b) {
            return mb_strlen($b, 'UTF-8') <=> mb_strlen($a, 'UTF-8');
        });

        return $map;
    }

    protected function replacePhemoji($html)
    {
        $emojiMap = $this->loadPhemojiAssets();

        foreach ($emojiMap as $sequence => $asset) {
            $newline = "\n";
            $img = $newline . '<img class="phemoji" src="/assets/phemoji/' . $asset . '"/>'. $newline;

            $html = str_replace($sequence, $img, $html);
        }

        return $html;
    }


    // Parsedown will call blockEmbed with ($Line, $CurrentBlock)
    public function blockEmbed($Line, $CurrentBlock = null){
        if (preg_match('/^!(\w+)\[(.+)\]$/', $Line['text'], $matches)){
                error_log("Pizzadown::blockEmbed called with: " . $Line['text']);
            $embedType = strtolower($matches[1]);
            $value = trim($matches[2]);
            $renderedHTML = $this->renderEmbed($embedType, $value);
            // Add a visible marker in the output so the page source shows the embed was processed
            //$renderedHTML .= "<!--PizzadownEmbed:{$embedType}-->" . $renderedHTML;

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
        if (isset($embedTypeClassMapping[$type])) {
            $embedHandlerClass = $embedTypeClassMapping[$type];
            $embedHandler = new $embedHandlerClass($value);
            return $embedHandler->render();
        }

                
    }

    public function inlineEmoticon($Excerpt, $CurrentBlock = null) {
        if (preg_match('/(\S+)/', $Excerpt['text'], $matches)) {
            $rendered = "";
            $emoticon = $matches[1];
            if (array_search($emoticon, array_keys($this->emoticonMap)) !== false) {
                $rendered = $this->emoticonMap[$emoticon];
            } else {
                return null;
            }
            return [
                'extent' => strlen($emoticon),
                'element' => [
                    'name' => 'span',
                    'text' => $rendered
                ]
            ];
        }
        return null;
    }


    #[Override]
    public function text($text)
    {
        
        return $this->replacePhemoji(parent::text($text));
    }

    
}
