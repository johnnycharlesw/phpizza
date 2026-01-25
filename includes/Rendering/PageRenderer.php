<?php
namespace PHPizza\Rendering;
use PHPizza\Addons\Skin;

/**
 * The page renderer class
 */
class PageRenderer{

    /**
     * Whether to treat body content as Markdown and convert to HTML.
     * Defaults to false; enable explicitly when desired (e.g., web UI).
     * @var bool
     */
    private $renderMarkdown = false;

    public function __construct(array $options = [])
    {
        if (isset($options['markdown'])) {
            $this->renderMarkdown = (bool)$options['markdown'];
        }
    }

    public function get_title_tag($sitename, $page_title = ''){
        if ($page_title){
            return "<title>$page_title - $sitename</title>";
        } else {
            return "<title>$sitename</title>";
        }
    }

    public function get_metadata_tags($description = '', $keywords = []){
        $meta_tags = '';
        if ($description){
            $meta_tags .= "<meta name=\"description\" content=\"$description\">\n";
        }
        if (!empty($keywords)){
            $keywords_content = implode(', ', $keywords);
            $meta_tags .= "<meta name=\"keywords\" content=\"$keywords_content\">\n";
        }
        return $meta_tags;
    }

    public function get_head_tags($sitename, $page_title = '', $description = '', $keywords = [], $useSkin = false){
        $head_tags = $this->get_title_tag($sitename, $page_title);
        $head_tags .= "\n" . $this->get_metadata_tags($description, $keywords);
        $head_tags .= "\n";
        $head_tags .= "<script src=\"phpizza-cms-js/console_message.js\"></script>";
        if ($useSkin){
            global $skinName;
            $skin_head_tags=$this->get_skin_head_tags($skinName);
            $head_tags .= "\n{$skin_head_tags}";
        }
        return $head_tags;
    }

    public function get_skin_head_tags(string $skinName, string $theme = "system"){
        $skin = new Skin($skinName);
        $skin->parse_manifest_json(false);
        if (isset($skin->manifest)) {
            $skinStylesheet='';
            foreach ($skin->manifest['stylesheets'] as $stylesheet) {
                $skinStylesheet .= $skinName . "/" . $stylesheet . ",";
            }
            if ($skin->manifest['themeStylesheets']) {
                global $theme;
                $theme = $theme ?? $_GET['usetheme'] ?? 'system';
                $skinStylesheet .= $skinName . "/" . $skin->manifest['themeStylesheets'][$theme];
            }
            $skinStylesheet=rtrim($skinStylesheet,",");
        }else{
            $skinStylesheet="style.css";
        }
        
        global $debug;
        if ($debug){
            $date=time();
            $skinStyleLinkTemplate = '<link rel="stylesheet" href="/load.php?t=css&f=%s/%s&v=' . $date . '">';
        } else {
            $skinStyleLinkTemplate = '<link rel="stylesheet" href="/load.php?t=css&f=%s/%s">';
        }
        global $theme;
        $theme = $theme ?? (isset($_GET['usetheme']) ? htmlspecialchars($_GET['usetheme']) : 'system');

        $skinStyleLinks = "";
        if (isset($skin->manifest)) {
            $links = [];
            $links[] = sprintf($skinStyleLinkTemplate, "phpizza-ui-framework", "style.css");
            // Add all default stylesheets
            foreach ($skin->manifest['stylesheets'] as $stylesheet) {
                $links[] = sprintf($skinStyleLinkTemplate, $skinName, $stylesheet);
            }
            // Add the theme-specific stylesheet if it exists
            if (isset($skin->manifest["themeStylesheets"][$theme])) {
                $links[] = sprintf($skinStyleLinkTemplate, $skinName, $skin->manifest["themeStylesheets"][$theme]);
            }
            $skinStyleLinks = implode("\n", $links);
        } else {
            // Fallback: single stylesheet
            $skinStyleLinks = sprintf($skinStyleLinkTemplate, $skinName, $skinStylesheet);
        }

        return $skinStyleLinks;
        #return "<link rel='stylesheet' href='/css.php?f=$skinName/$skinStylesheet'>";
    }

    public function get_head_tag_html($sitename, $page_title = '', $description = '', $keywords = [], $useSkin = true){
        $innerHTML= $this->get_head_tags($sitename, $page_title, $description, $keywords, $useSkin);
        return <<<HTML
<head>
    {$innerHTML}
</head>
HTML;
    }

    public function get_skin_body_innerHTML(string $skinName, $innerHTML = ''){
        $skin = new Skin($skinName);
        return <<<HTML
<style>
    .{$skin->get_skin_class()}, body {
        width:100%;
        height:100%;
    }
</style>
<div class="{$skin->get_skin_class()}">
    <header>
        {$skin->get_header()}
    </header>
    <aside class="sidebar">
        {$skin->get_sidebar()}
    </aside>
    <article class="phpizza-content">
        {$innerHTML}
    </article>
    <footer>
        {$skin->get_footer()}
    </footer>
</div>
<script src='/load.php?t=js&f=prismjs/prism.js'></script>
HTML;
    }

    public function get_body_tag_html($innerHTML = '', $useSkin = false){
        if ($useSkin){
            global $skinName;
            $innerHTML_=$this->get_skin_body_innerHTML($skinName, $innerHTML);
        } else {
            $innerHTML_=$innerHTML;
        }
        return $this->_get_body_tag_html($innerHTML_,$useSkin);
    }

    public function _get_body_tag_html($innerHTML = '', $useSkin=false){
        // Only convert Markdown when explicitly enabled
        if ($this->renderMarkdown && class_exists('PHPizza\Rendering\Pizzadown')) {
            try {
                $pd = new Pizzadown();
                $innerHTML = $pd->text($innerHTML);
            } catch (\Throwable $e) {
                // fall back to raw content on error
            }
        }


        return <<<HTML
<body>
    {$innerHTML}
</body>
HTML;
    }

    /**
     * This function returns an HTML page as a string
     * 
     * @param string $sitename The website's name, set this to $sitename the global variable in the full CMS.
     * @param string $page_title The title of the page
     */
    public function get_html_page(
        $sitename, 
        $page_title = '', 
        $description = '', 
        $keywords = [], 
        $body_innerHTML = '', 
        $siteLanguage = 'en',
        $useSkin = true,
    ) : string {
        $headHTML = $this->get_head_tag_html($sitename, $page_title, $description, $keywords);
        $bodyHTML = $this->get_body_tag_html($body_innerHTML,$useSkin);
        return <<<HTML
<!DOCTYPE html>
<html lang="{$siteLanguage}">
    {$headHTML}
    {$bodyHTML}
</html>
HTML;
    }
}