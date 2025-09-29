<?php
namespace PHPizza;


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

    public function get_head_tags($sitename, $page_title = '', $description = '', $keywords = []){
        $head_tags = $this->get_title_tag($sitename, $page_title);
        $head_tags .= "\n" . $this->get_metadata_tags($description, $keywords);
        return $head_tags;
    }

    public function get_head_tag_html($sitename, $page_title = '', $description = '', $keywords = []){
        $innerHTML= $this->get_head_tags($sitename, $page_title, $description, $keywords);
        return <<<HTML
<head>
    {$innerHTML}
</head>
HTML;
    }

    public function get_body_tag_html($innerHTML = ''){
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
        $siteLanguage = 'en'
    ) : string {
        $headHTML = $this->get_head_tag_html($sitename, $page_title, $description, $keywords);
        $bodyHTML = $this->get_body_tag_html($body_innerHTML);
        return <<<HTML
<!DOCTYPE html>
<html lang="{$siteLanguage}">
    {$headHTML}
    {$bodyHTML}
</html>
HTML;
    }
}