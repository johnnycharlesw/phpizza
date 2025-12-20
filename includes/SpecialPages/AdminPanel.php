<?php
namespace PHPizza\SpecialPages;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\SpecialPages\CreateAccount;
use PHPizza\SpecialPages\Editor;
use PHPizza\SpecialPages\Settings;
use PHPizza\SpecialPages\OGTestHomepage;

class AdminPanel extends SpecialPage {
    private $userdb;
    private $configdb;
    public function __construct() {
        global $sitename;
        parent::__construct("AdminPanel", "PHPizza Admin Panel", ""); # Content will be generated automatically
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }
    public function getContent()
    {
        // Prevent unauthorized users from accessing this
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $accesser=$this->userdb->get_user_by_id($_SESSION['user_id']);
        if (!$accesser->can_I_do("use_admin_panel")) {
            http_response_code(403);
            return <<<HTML
            <h1>Access Denied</h1>
            <p>You do not have permission to access the admin panel.</p>
            HTML;
        }

        $sections = [
            'main' => OGTestHomepage::class,
            'create_account' => CreateAccount::class,
            'editor' => Editor::class,
            'settings' => Settings::class
        ];
        $section = $_GET['section'] ?? 'main';
        global $specialPrefix;
        $specialPage=new $sections[$section]();
        $sectionContent = $specialPage->getContent();
        $barContent="<ul>";
        foreach (array_keys($sections) as $section_){
            $barContent .= <<<HTML
<li>
    <a href="/index.php?title={$specialPrefix}AdminPanel&section={$section_}">{$section_}</a>
</li>
HTML;
        }
        $barContent .= "</ul>";
        $content = <<<HTML
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