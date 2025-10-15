<?php
namespace PHPizza;

class SQLite {
    private $dbServer;
    private $dbUser;
    private $dbPassword;
    private $dbName;
    private $dbInterface;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName) {
        $this->dbServer = $dbServer;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        
        if ($this->dbServer !== "localhost" || $this->dbServer !== "127.0.0.1") {
            error_log("Warning: SQLite is not a client-server DBMS, therefore {$this->dbServer} is not a valid DB server");
        }
        $this->dbInterface = new \SQLite3($dbName);

    }

    public function fetchAll($query, $params = [], $types = '') {
        return $this->dbInterface->query($query, $params, $types);
    }

    public function fetchRow($query, $params = [], $types = '') {
        return $this->dbInterface->query($query, $params, $types)->fetchArray();
    }

    public function execute($query, $params = [], $types = '') {
        return $this->dbInterface->exec($query, $params, $types);
    }

    public function getLastInsertId() {
        return $this->dbInterface->lastInsertRowID();
    }

}