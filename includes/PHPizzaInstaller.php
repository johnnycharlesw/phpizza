<?php
namespace PHPizza;

class PHPizzaInstaller {
    private Database $db; # It is not always MariaDB
    private UserDatabase $userdb;
    private UserGroupDatabase $groupdb;
    private Pizzadown $pd;
    private array $admin_creds;
    private array $dbcreds;
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
            $adminPassword,
            $adminEmail
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

# Guest credentials (defaults defined in schema)
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
        // I HAVE A SCHEMA FILE IN sql/create_schema_mariadb.sql for mariadb, follow the sae pattern for each dbtype. Canonicalize references to PostgreSQL to "postgresql" when looking up the filename.
        switch ($dbType) {
            case "mariadb":
                $filename = "sql/create_schema_mariadb.sql";
                break;
            case "mysql":
                $filename = "sql/create_schema_mysql.sql";
                break;
            case "percona":
                $filename = "sql/create_schema_mysql.sql";
                break;
            case "myrocks":
                $filename = "sql/create_schema_myrocks.sql";
                break;
            case "postgresql":
                $filename = "sql/create_schema_postgresql.sql";
                break;
            case "pgsql":
                $filename = "sql/create_schema_postgresql.sql";
                break;
            case "postgre":
                $filename = "sql/create_schema_postgresql.sql";
                break;
            case "sqlite":
                $filename = "sql/create_schema_sqlite.sql";
                break;
            default:
                throw new Exception("Unsupported database type: $dbType");
        }

        // Execute the SQL script
        $sql=file_get_contents(realpath(__DIR__) . "/$filename");
        $result=$this->db->execute($sql);
        if ($result === false) {
            throw new Exception("Failed to execute SQL script: " . $this->db->error);
        }
        
        // Now it is safe to initialize userdb, so do so
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->groupdb=new UserGroupDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
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