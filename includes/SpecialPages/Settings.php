<?php
namespace PHPizza\SpecialPages;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\ConfigurationDatabase;

class Settings extends SpecialPage {
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

        // Generate the content for the settings page
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $content = <<<HTML
            <style>
                ul.tab-bar{
                    list-style-type:none;
                    display:flex;
                }
            </style>
            <ul class="tab-bar">
            HTML;
            $tabs=[
                "general" => "General Settings",
                "clientidentity" => "Client Identity settings"
            ];

            foreach ($tabs as $id => $name) {
                $content .= <<<HTML
                <li>
                    <a href="/index.php?title={$specialPrefix}AdminPanel&section=settings&tab={$id}">{$name}</a>
                </li>
                HTML;
            }
            $content .= "\n</ul>";
            global $sitename, $siteLanguage, $skinName;
            $tab=$_GET["tab"] ?? 'general';
            if ($tab=="general"){
                $content .= <<<HTML
                <h2>General Settings</h2>
                <form method="post" action="/index.php?title={$specialPrefix}AdminPanel&section=settings">
                    <label for="site_name">Site Name:</label>
                    <input type="text" id="site_name" name="sitename" value="{$sitename}">
                    <br>
                    <label for="site_description">Site Language:</label>
                    <input type="text" id="site_language" name="siteLanguage" value="{$siteLanguage}">
                    <br>
                    <label for="site_description">Skin name:</label>
                    <input type="text" id="site_description" name="skinName" value="{$skinName}">
                    <br>
                    <input type="submit" value="Save">
                </form>
                HTML;
            } elseif ($tab=="clientidentity") {
                $content .= <<<HTML
                <h2>Client Identity Settings</h2>
                <form method="post" action="/index.php?title={$specialPrefix}AdminPanel&section=settings">
                    <label for="allow_tor">Allow Tor Browser:</label>
                    <select name="allowTor" id="allow_tor">
                        <option value="true">Allow</option>
                        <option value="false" selected>Deny</option>
                    </select>
                    <br>
                    <label for="allow_proxy">Allow Proxy:</label>
                    <select name="allowProxy" id="allow_proxy">
                        <option value="true" selected>Allow</option>
                        <option value="false">Deny</option>
                    </select>
                    <br>
                    <label for="allow_vpn">Allow VPNs:</label>
                    <select name="allowVPN" id="allow_vpn">
                        <option value="true" selected>Allow</option>
                        <option value="false">Deny</option>
                    </select>
                    <br>
                    <label for="allow_datacenter">Allow unofficial APIs:</label>
                    <select name="allowDatacenter" id="allow_vpn">
                        <option value="true">Allow</option>
                        <option value="false" selected>Deny</option>
                    </select>
                    <br>
                    <label for="allow_mobile_data">Allow mobile data:</label>
                    <select name="allowMobileData" id="allow_mobile_data">
                        <option value="true" selected>Allow</option>
                        <option value="false">Deny</option>
                    </select>
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
            $accesser=$this->userdb->get_user_by_id($_SESSION['user_id']);
            if (!$accesser->can_I_do("use_admin_panel")) {
                http_response_code(403);
                return <<<HTML
                <h1>Access Denied</h1>
                <p>You do not have permission to access the admin panel.</p>
                HTML;
            }

            foreach ($_POST as $key => $value) {
                $this->configdb->set_value($key, $value);
            }
            header("Location: /index.php?title={$specialPrefix}AdminPanel&section=settings&tab=general");
        }
    }
}