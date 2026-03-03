<?php
namespace PHPizza\DNS;
use PHPizza\Database\Database;
use PHPizza\Database\DatabaseException;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\User;

class ConnectedGandiAccountDatabase {
    private Database $db;
    private UserDatabase $userdb;
    
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        if (!$this->db->get_table_exists('gandi_api_keychain')) {
            throw new DatabaseException("The Gandi.net API Keychain does not exist in the database, please update the schema.");
        }
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function get_gandi_account_by_user(User $user) {
        
    }
}