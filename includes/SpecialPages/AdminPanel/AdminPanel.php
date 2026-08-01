<?php
namespace PHPizza\SpecialPages\AdminPanel;

use PHPizza\Rendering\_403Renderer;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\SpecialPages\CreateAccount;
use PHPizza\SpecialPages\SpecialPage;
use PHPizza\SpecialPages\AdminPanel\OGTestHomepage;

class AdminPanel extends SpecialPage {
    private $userdb;
    private $configdb;
    private $args;

    public function __construct($name, $title, $content) {
        global $sitename;
        parent::__construct($name, "PHPizza Admin Panel", ""); # Content will be generated automatically
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->args = explode('/', $name, PHP_INT_MAX);

    }
    public function getContent()
    {
        // Prevent unauthorized users from accessing this
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $accesser=$this->userdb->get_user_by_id($_SESSION['user_id']);
        if ($accesser->am_I_an_admin() === false) {
            http_response_code(403);
            $_403renderer = new _403Renderer('access the admin panel');
            return $_403renderer->render();
        }


        $sections = [
            'main' => OGTestHomepage::class,
            'editor' => Editor::class,
            'settings' => Settings::class
        ];
        $section = $this->args[1] ?? $_GET['section'] ?? 'main';
        global $specialPrefix;
        $specialPage=new $sections[$section]();
        $sectionContent = $specialPage->getContent();
        $barContent="<ul>";
        global $debug;
        if ($debug) {
            $barContent .= "<li><p>Developer mode enabled!</p></li>";
        }
        foreach (array_keys($sections) as $section_){
            $barContent .= <<<HTML
<li>
    <a href="/index.php?title={$specialPrefix}AdminPanel&section={$section_}">{$section_}</a>
</li>
HTML;
        }

        $barContent .= "</ul>";
        // Datacenter check
        $serverip = $_SERVER["SERVER_ADDR"];
        $ipquery_json = file_get_contents("https://api.ipquery.io/" . $serverip);
        $ipquery = json_decode($ipquery_json, true);
        if ($ipquery['risk']['is_datacenter']) {
            $content = <<<HTML
            <div id="datacenter-warning-banner">
                ⚠️ WARNING: Your site seems to be running in a datacenter. PHPizza does not support hosting on third-party VPS services, and support will not be offered for such instances. Please self-host your site if you can safely do so.
            </div><br>
            HTML;
        } else {
            $content = "";
        }

        $content .= <<<HTML
<link rel="stylesheet" href="/load.php?t=css&f=phpizza-css/admin.css" />
<div class="phpizza-admin-panel-main">
    
<aside class="phpizza-admin-panel-sidebar">
    {$barContent}
</aside>
<main style="display:block">
{$sectionContent}
</main>
<aside class="phpizza-admin-panel-sidebar">

</aside>
</div>
HTML;
return $content;

    }
}