<?php
namespace PHPizza;

use function Adminer\dump_csv;

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
        if ($useSkin){
            global $skinName;
            $skin_head_tags=$this->get_skin_head_tags($skinName);
            $head_tags .= "\n{$skin_head_tags}";
        }
        return $head_tags;
    }

    public function get_skin_head_tags(string $skinName, string $theme = "system"){
        $skin = new Skin($skinName);
        if (isset($skin->manifest)) {
            $skinStylesheet='';
            foreach ($skin->manifest->stylesheets as $stylesheet) {
                $skinStylesheet .= $skinName . "/" . $stylesheet . ",";
            }
            if ($skin->manifest->themeStylesheets) {
                $skinStylesheet .= $skinName . "/" . $skin->manifest->themeStyleSheets[$theme];
            }
            $skinStylesheet=rtrim($skinStylesheet,",");
        }else{
            $skinStylesheet="style.css";
        }
        
        $skinStyleLinkTemplate = <<<HTML
<link rel="stylesheet" href="css.php?f={{skinName}}/{{skinStyleSheet}}"></link>
HTML;
        $skinStyleLinks="";
        if (isset($skin->manifest)) {
            foreach ($skin->manifest->stylesheets as $stylesheet) {
                $skinStyleLink=$skinStyleLinkTemplate;
                $skinStyleLink=str_replace("{{skinName}}",$skinName,$skinStyleLink);
                $skinStyleLink=str_replace("{{skinStyleSheet}}",$stylesheet,$skinStyleLink);
                $skinStyleLinks .= "\n" . $skinStyleLink;
            }
        }
        

        #return $skinStyleLink;
        return "<link rel='stylesheet' href='css.php?f=$skinName/$skinStylesheet'></link>";
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
        if ($this->renderMarkdown && class_exists('Parsedown')) {
            try {
                $pd = new \Parsedown();
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