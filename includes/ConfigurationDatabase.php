<?php
namespace PHPizza;
use PHPizza\Database\Database;

class ConfigurationDatabase {
    private $db;
    # Loads in config from the site_settings table
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function load_config() {
        // Load the configuration from the site_settings table
        $varDenylist=[
            'dbServer', # Database configuration
            'dbUser',
            'dbPassword',
            'dbName',
            'dbType',
        ];

        $query = "SELECT * FROM site_settings";
        $result = $this->db->fetchAll($query);
        foreach ($result as $row) {
            $GLOBALS[$row['key']] = $row['value'];
        }
    }

    public function set_value($key, $value) {
        // Set a configuration value
        $query = "UPDATE site_settings SET `value` = ? WHERE `key` = ?";
        $this->db->execute($query, [$value, $key]);
        $GLOBALS[$key] = $value;
    }

    public function register_key($key, $value) {
        // Register a new configuration key
        $query = "INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)";
        $this->db->execute($query, [$key, $value]);
        $GLOBALS[$key] = $value;
    }
}