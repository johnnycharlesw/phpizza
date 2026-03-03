<?php
namespace PHPizza\Installer;
use PHPizza\Database\Database;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\UserGroupDatabase;
use PHPizza\Rendering\Pizzadown;
use PHPizza\Updates\SchemaMigrator;
use PHPIzza\Exception;
use PHPizza\PageManagement\PageDatabase;

class PHPizzaInstaller {
    private Database $db; # It is not always MariaDB
    private UserDatabase $userdb;
    private UserGroupDatabase $groupdb;
    private PageDatabase $pagedb;
    private Pizzadown $pd;
    private SchemaMigrator $schema_migrator;
    public array $admin_creds;
    public array $dbcreds;
    public string $pagetitle;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType, $adminUsername, $adminPassword, $adminEmail, $sitename, $siteLanguage) {
        $this->dbcreds = [
            'server' => $dbServer,
            'user' => $dbUser,
            'password' => $dbPassword,
            'database' => $dbName,
            'type' => $dbType,
        ];
        $this->db=new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        # THE SCHEMA HAS NOT BEEN APPLIED AT THIS STAGE! INITIALIZING USERDB NOW WOULD BE UNSAFE!
        $this->admin_creds=[
            "username" => $adminUsername,
            "password" => $adminPassword,
           "email" => $adminEmail
            ];
        $siteName=$sitename;
        global $sitename;
        $sitename=$siteName;
        $siteLanguage_=$siteLanguage;
        global $siteLanguage;
        $siteLanguage=$siteLanguage_;
        $this->pagetitle=$sitename;
    }

    public function generate_config_str(){
        global $sitename, $siteLanguage;
        $cnf=<<<PHP
<?php
# Database credentials
\$dbServer = '{$this->dbcreds["server"]}';
\$dbUser = '{$this->dbcreds["user"]}';
# DB Password encoded in /passwd.b64
\$dbName = '{$this->dbcreds["database"]}';
\$dbType = '{$this->dbcreds["type"]}';

# Guest credentials (defaults defined in installer)
\$guestUsername="Guest";
\$guestPasswordB64="aUFtQUd1ZXN0"; // base64 for "iAmAGuest"
# Admin credentials are in the DB for security reasons

# Site configuration
\$sitename = '{$sitename}';
\$siteLanguage = '{$siteLanguage}';

# Skin and extension system
\$useSkin=true;
\$skinName="PHPizza";
/*
use function PHPizza\loadExtension;
loadExtension("PHPizzaCMSTestExtension");
*/

\$homepageName="home";
PHP;
        file_put_contents(realpath(__DIR__) . "/passwd.b64", base64_encode($this->dbcreds["password"]));
        file_put_contents(realpath(__DIR__) . "/config.php", $cnf);
        return $cnf;
    }

    public function init_db(){
        global $dbServer, $dbUser, $dbName, $dbType, $dbPassword;
        $this->schema_migrator = new SchemaMigrator();

        // Initialize schema
        $this->schema_migrator->migrate();
        
        // Now it is safe to initialize userdb, so do so
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->groupdb=new UserGroupDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->pagedb=new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function create_admin_user(){
        $admins_account=$this->userdb->create_user(
            $this->admin_creds["username"],
            $this->admin_creds["password"]
        );
        $this->userdb->update_user_email($admins_account->getId(), $this->admin_creds["email"]);
        $this->groupdb->add_user_to_group($admins_account->getId(), "admin");
        # Tie PHPSESSID to the client and the admin account
        session_start();
        $_SESSION["user_id"]=$admins_account->getId();
        $_SESSION["username"]=$admins_account->getUsername();
        return true;
    }

    public function create_guest_account(){
        $guestAccount = $this->userdb->create_user('Guest', 'iAmAGuest');
    }

    public function create_default_homepage(){
        $homepageContents = file_get_contents(__DIR__ . '/sql/schema/default_data/pages/home.md');
        global $homepageName;
        $this->pagedb->createPage($homepageName, $homepageContents);
    }

    public function create_groups(){
        # Groups
        $defaultGroups = [
            "admin",
            "editor"
        ];
        
    }

    public function install(){
        global $debug;
        if ((!$debug) && file_exists(realpath(__DIR__) . "/config.php")) {
            throw new Exception("PHPizza was already installed.");
        }
        
        $this->generate_config_str();
        $this->init_db();
        $this->create_admin_user();
        return true;
    }
}