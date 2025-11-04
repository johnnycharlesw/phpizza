<?php
namespace PHPizza;

class SpecialPageSettings extends SpecialPage {
    private $userdb;
    private $configdb;
    public function __construct() {
        global $sitename;
        parent::__construct("SiteSettings", "Settings", ""); # Content will be generated automatically
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->configdb=new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function getContent() {
        global $specialPrefix;
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


        // Generate the content for the settings page
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $content = <<<HTML
            <style>
                ul.tab-bar{
                    list-style-type:none;
                    display:flex;
                }
            </style>
            <h1>Settings</h1><br>
            <ul class="tab-bar">
            HTML;
            $tabs=[
                "general" => "General Settings"
            ];

            foreach ($tabs as $id => $name) {
                $content .= <<<HTML
                <li>
                    <a href="/index.php?title={$specialPrefix}SiteSettings?tab={$id}">{$name}</a>
                </li>
                HTML;
            }
            $content .= "\n</ul>";
            global $sitename, $siteLanguage, $skinName;
            $tab=$_GET["tab"];
            if ($tab=="general"){
                $content .= <<<HTML
                <h2>General Settings</h2>
                <form method="post" action="/index.php?title={$specialPrefix}SiteSettings">
                    <label for="site_name">Site Name:</label>
                    <input type="text" id="site_name" name="sitename" value="{$sitename}">
                    <br>
                    <label for="site_description">Site Language:</label>
                    <input type="text" id="site_description" name="siteLanguage" value="{$siteLanguage}">
                    <br>
                    <label for="site_description">Skin name:</label>
                    <input type="text" id="site_description" name="skinName" value="{$skinName}">
                    <br>
                    <input type="submit" value="Save">
                </form>
                HTML;
            } else {
                http_response_code(404);
                return <<<HTML
                <h1>Page Not Found</h1>
                <p>The specified tab does not exist.</p>
                HTML;
            }
            return $content;
        } elseif ($_SERVER["REQUEST_METHOD"]="POST") {
            # Parameters when POSTed to this script align with config.php variable names
            foreach ($_POST as $key => $value) {
                $this->configdb->set_value($key, $value);
            }
            header("Location: /index.php?title={$specialPrefix}SiteSettings&tab=general");
        }
    }
}