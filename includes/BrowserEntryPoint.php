<?php
namespace PHPizza;
global $dbType;
use PHPizza\PageRenderer;
use PHPizza\PageDatabase;
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
        $page_id = isset($_GET['title']) ? $_GET['title'] : 'home';

        // Pull DB config variables (provided by init.php)
        global $dbServer, $dbUser, $dbPassword, $dbName, $sitename, $siteLanguage;

        // Initialize PageDatabase using config variables
        $pagedb = new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $page = $pagedb->getPage($page_id);

        if ($page) {
            http_response_code(200);
            $page_title = $page['title'];
            $page_content = $page['content'];
            $description = substr(strip_tags($page_content), 0, 150); // Simple description
            $keywords = [];
            if (!empty($page['keywords'])) {
                $keywords = array_map('trim', explode(',', $page['keywords'])); // Assuming keywords are stored as comma-separated values
            }
        } else {
            http_response_code(404);
            $page_title = "Page Not Found";
            $page_content = "<h1>404 Not Found</h1><p>The requested page does not exist.</p>";
            $description = "The requested page does not exist.";
            $keywords = [];
        }

        // Render page
        echo $this->pageRenderer->get_html_page(
            $sitename,
            $page_title,
            $description,
            $keywords,
            $page_content,
            $siteLanguage
        );

    }
}
