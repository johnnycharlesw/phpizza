<?php
namespace PHPizza;

use Dom\HTMLElement;
use PHPizza\PageRenderer;
use PHPizza\PageDatabase;

use function Safe\file_get_contents;

/**
 * Handles a browser request for a single page.
 *
 * Starts/resumes the session, reads the 'title' GET parameter (default 'home'),
 * retrieves the page via PageDatabase, sets the HTTP status (200 or 404),
 * prepares basic metadata (title, description, keywords), and renders HTML via PageRenderer.
 *
 * Side effects: Sends HTTP status headers and outputs the full HTML response.
 *
 * @global string $dbServer     Database host.
 * @global string $dbUser       Database user.
 * @global string $dbPassword   Database password.
 * @global string $dbName       Database name.
 * @global string $sitename     Site display name.
 * @global string $siteLanguage Site language code.
 *
 * @return void
 */
class BrowserEntryPoint
{
    private $pageRenderer;

    public function __construct() {
        // Initialize classes used
        $this->pageRenderer = new PageRenderer();
    }

     /**
    * Handles a browser request for a single page.
    *
    * Starts/resumes the session, reads the 'title' GET parameter (defaults to 'home'),
    * retrieves the page from the database, sets the appropriate HTTP status (200/404),
    * prepares basic metadata (title, description, keywords), and echoes the rendered HTML.
    *
     * Side effects: Sends HTTP status headers and outputs the full HTML response.
    *
     * @global string $dbServer      Database host.
     * @global string $dbUser        Database user.
     * @global string $dbPassword    Database password.
     * @global string $dbName        Database name.
     * @global string $dbType        Database type.
     * @global string $sitename      Site display name.
     * @global string $siteLanguage  Site language code.
     *
     * @return void
     */
    public function run()
    {
        
        

        // Start a PHP session or load one if one already exists
        session_start();
        
        // Handle GET parameters
        global $homepageName;
        $page_id = isset($_GET['title']) ? $_GET['title'] : $homepageName;
        $is_editor = isset($_GET['editing']) ? (bool)$_GET['editing'] && $_GET["editing"]==="true" : false;

        // Check for updates and install updates if available
        if ($page_id == $homepageName) {
            $updater = new Updater();
            if ($updater->get_is_available()) {
                $updater->install_updates_if_available();
            }
            
        }
        

        // Pull DB config variables (provided by init.php)
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType, $sitename, $siteLanguage;

        // Initialize PageDatabase using config variables
        $pagedb = new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $page = $pagedb->getPage($page_id);

        // Initialize parsedown
        $parsedown = new \Parsedown();

        if ($page) {
            http_response_code(200);

            $page_title = $page['title'];
            #var_dump($is_editor);
            #$is_editor=(bool)$is_editor && $is_editor == "true";

            if ($is_editor) {
                var_dump(__DIR__);
                var_dump(__DIR__ . "/editor-ui.md");
                $editor_ui_md=file_get_contents(__DIR__ . "/editor-ui.md");
                $vars =[
                    'md-contents' => $page['content'],
                ];
                foreach ($vars as $key => $value) {
                    $editor_ui_md=str_replace("{{" . $key . "}}", htmlspecialchars($value), $editor_ui_md);
                }
                $page_content=$parsedown->text($editor_ui_md);
                
            }
            else{
                $page_content = $parsedown->text($page['content']);
            }
            

            $description = substr(strip_tags($page_content), 0, 150); // Simple description
            $keywords = [];
            if (!empty($page['keywords'])) {
                $keywords = array_map('trim', explode(',', $page['keywords'])); // Assuming keywords are stored as comma-separated values
            }
        } else {
            http_response_code(404);
            $page=$pagedb->getPage("404");
            if ($page) {
                $page_title = $page['title'];
                $page_content = $parsedown->text($page['content']);
                $description = substr(strip_tags($page_content), 0, 150); // Simple description
                $keywords = [];
                if (!empty($page['keywords'])) {
                    $keywords = array_map('trim', explode(',', $page['keywords'])); // Assuming keywords are stored as comma-separated values
                }
            } else {
                $page_title = "404 Not Found";
                $page_content = <<<HTML
<h1>404 Not Found</h1>
<p>
    It appears the page you were looking for has not been found, AND the owner of this site didn't put this 404 page here. What a coincidence.
</p>
HTML;
                $description = substr(strip_tags($page_content), 0, 150); // Simple description
                $keywords = [];
                
            }
            
        }

        // Render page
        echo $this->pageRenderer->get_html_page(
            $sitename,
            $page_title,
            $description,
            $keywords,
            $page_content,
            $siteLanguage,
            $useSkin=true
        );

    }
    
};