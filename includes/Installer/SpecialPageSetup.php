<?php
namespace PHPizza\Installer;
use PHPizza\SpecialPages\SpecialPage;

# GUI installer for PHPizza (if config.php is not found, it will be used as a part of the installer)
class SpecialPageSetup extends SpecialPage {
    private $installer;
    public function __construct() {
        global $isInstaller;
        parent::__construct("Setup", "Setup", ""); # Content will be generated automatically
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType; # Umm, no, the PHPizzaInstaller object is looking for the database config variables of the target db, not the installer db, but we will change it when that info has been obtained
        $this->installer = new PHPizzaInstaller($dbServer, $dbUser, $dbPassword, $dbName, $dbType,"Admin","","admin-who-has-not-finished-the-phpizza-setup@example.org","PHPizza site","en-US");
    }

    public function set_admin_creds($username, $password, $email) {
        $this->installer->admin_creds = [
            "username" => $username,
            "password" => $password,
            "email" => $email
        ];
    }

    public function point_installer_backend_to_target_db($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->installer->dbcreds = [
            'server' => $dbServer,
            'user' => $dbUser,
            'password' => $dbPassword,
            'database' => $dbName,
            'type' => $dbType
        ];
    }
    
    public function install(){
        # Call the installer backend to install the PHPizza site
        $this->installer->install();

        # Clean up afterward
        include 'init.php'; # Switch the PHPizza context to the installed site from the installer site
        # Wipe the temp files used to render the installer
        unlink("includes/Installer/phpizza_installer.sqlite3");
        unlink("includes/Installer/phpizza_installer.sqlite3.bak");
        # Keep installer-config.php, as it is needed to reinstall if something goes wrong
    }

    public function handle_form_submission() {
        $tab = $_POST["tab"];
        if ($tab === "admin_creds"){
            $this->set_admin_creds($_POST["username"], $_POST["password"], $_POST["email"]);
        } elseif ($tab === "db_config"){
            $this->point_installer_backend_to_target_db($_POST["dbServer"], $_POST["dbUser"], $_POST["dbPassword"], $_POST["dbName"], $_POST["dbType"]);
        } elseif ($tab === "install") {
            $this->install();
        } else {
            echo "Invalid tab selected";
        }
    }

    public function render_installation_ui() {
        global $specialPrefix;
        $content = <<<HTML
        <style>
            ul.tab-bar{
                list-style-type:none;
                display:flex;
            }
        </style>
        <h1>Setup</h1><br>
        <ul class="tab-bar">
        HTML;
        $tabs=[
            "admin_creds" => "Admin Credentials",
            "db_config" => "Database Configuration"
        ];
    }
    public function getContent() {
        // Pick between installation UI and installation process based on request method
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handle_form_submission();
        } else {
            $this->render_installation_ui();
        }
    }
}