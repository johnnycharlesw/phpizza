<?php
namespace PHPizza\EntryPoints;

use Dom\HTMLElement;
use PHPizza\Rendering\PageRenderer;
use PHPizza\Rendering\Pizzadown;
use PHPizza\PageManagement\PageDatabase;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\User;
use PHPizza\HTTPHandling\HTTPEndpointHandler;
use PHPizza\Updates\Updater;

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
class BrowserEntryPoint extends HTTPEndpointHandler
{
    private $pageRenderer;

    public function __construct() {
        // Initialize classes used
        parent::__construct();
        $this->pageRenderer = new PageRenderer();
    }

    public function signInAsUser($username, $password) {
        // Start a PHP session or load one if one already exists
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Pull DB config variables (provided by init.php)
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;

        // Initialize UserDatabase using config variables
        $userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        if ($userdb->verify_user_credentials($username, $password)) {
            $user = $userdb->get_user_by_username($username);
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                return true;
            }
        }else {
            return false;
        }
    }

    public function logOutUser(){
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }
        // Set the user login variables back to defaults
        $_SESSION['user_id'] = null;
        $_SESSION['username'] = null;
        // Log in user as guest to avoid null session issues
        global $guestUsername, $guestPasswordB64;
        $this->signInAsUser($guestUsername,base64_decode($guestPasswordB64));
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
        global $sitename, $siteLanguage;
        
        // Dynamically change PHP config according to PHPizza config variables
        global $debug;
        if (isset($debug) && $debug){
            ini_set('display_errors',true);
        }else{
            ini_set('display_errors',false);
            error_reporting(0);
        }
        

        // Start a PHP session or load one if one already exists
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->logOutUser();
        }
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

        // Build page data via helper and render
        $data = $this->buildPageData($page_id, $is_editor);

        // Ensure correct HTTP status code is set
        $this->setStatusCode($data['status']);

        $html = $this->pageRenderer->get_html_page(
            $sitename,
            $data['title'],
            $data['description'],
            $data['keywords'],
            $data['html'],
            $siteLanguage,
            $useSkin=true
        );

        $this->setBody($html);
        $this->send_response_to_client();
    }


    /**
     * Build structured page data for a given page id.
     * Returns an array: ['status'=>int,'title'=>string,'html'=>string,'description'=>string,'keywords'=>array]
     */
    protected function buildPageData(string $page_id, bool $is_editor = false): array {
        // Pull DB config variables (provided by init.php)
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType, $sitename, $siteLanguage;

        // Initialize PageDatabase using config variables
        $pagedb = new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $page = $pagedb->getPage($page_id);

        // Initialize pizzadown
        $parsedown = new Pizzadown();

        if ($page) {
            $status = 200;
            $page_title = $page['title'];

            if ($is_editor) {
                $editor_ui_md = '';
                if (is_readable(__DIR__ . "/editor-ui.md")) {
                    $editor_ui_md = file_get_contents(__DIR__ . "/editor-ui.md");
                }
                // Extract embed lines, replace with tokens so Parsedown doesn't escape HTML
                $embed_map = [];
                $i = 0;
                $editor_ui_md = preg_replace_callback('/^!(\w+)\[(.+)\]$/m', function($m) use ($parsedown, &$embed_map, &$i){
                    $type = strtolower($m[1]);
                    $value = trim($m[2]);
                    $html = $parsedown->renderEmbed($type, $value);
                    $token = "PHPIZZA-EMBED-" . (++$i) . "-TOKEN";
                    $embed_map[$token] = $html;
                    return $token;
                }, $editor_ui_md);

                $page_content = $parsedown->text($editor_ui_md);
                if (!empty($embed_map)) {
                    $page_content = str_replace(array_keys($embed_map), array_values($embed_map), $page_content);
                }
            } else {
                // Extract embed lines, replace with tokens so Parsedown doesn't escape HTML
                $embed_map = [];
                $i = 0;
                $page_raw = preg_replace_callback('/^!(\w+)\[(.+)\]$/m', function($m) use ($parsedown, &$embed_map, &$i){
                    $type = strtolower($m[1]);
                    $value = trim($m[2]);
                    $html = $parsedown->renderEmbed($type, $value);
                    $token = "PHPIZZA-EMBED-" . (++$i) . "-TOKEN";
                    $embed_map[$token] = $html;
                    return $token;
                }, $page['content']);
                $page_content = $parsedown->text($page_raw);
                if (!empty($embed_map)) {
                    $page_content = str_replace(array_keys($embed_map), array_values($embed_map), $page_content);
                }
            }

            $description = substr(strip_tags($page_content), 0, 150); // Simple description
            $keywords = [];
            if (!empty($page['keywords'])) {
                $keywords = array_map('trim', explode(',', $page['keywords'])); // Assuming keywords are stored as comma-separated values
            }

            return [
                'status' => $status,
                'title' => $page_title,
                'html' => $page_content,
                'description' => $description,
                'keywords' => $keywords,
            ];
        }
        // 404 handling
        $status = 404;
        $page404 = $pagedb->getPage("404");
        if ($page404) {
            $page_title = $page404['title'];
            $page_content = $parsedown->text($page404['content']);
            $description = substr(strip_tags($page_content), 0, 150);
            $keywords = [];
            if (!empty($page404['keywords'])) {
                $keywords = array_map('trim', explode(',', $page404['keywords']));
            }
        } else {
            $page_title = "404 Not Found";
            $page_content = "<h1>404 Not Found</h1><p>It appears the page you were looking for has not been found.</p>";
            $description = substr(strip_tags($page_content), 0, 150);
            $keywords = [];
        }

        return [
            'status' => $status,
            'title' => $page_title,
            'html' => $page_content,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }
}
