<?php
namespace PHPizza;


class MariaDB {
    private $dbServer;
    private $dbUser;
    private $dbPassword;
    private $dbName;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName) {
        $this->dbServer = $dbServer;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        $this->init_database($this->dbServer, $this->dbUser, $this->dbPassword, $this->dbName);
    }

    public function fetchAll($query, $params = [], $types = '') {
        $stmt = $this->run_query($query, $params, $types);
        return $this->fetch_all($stmt);
    }

    public function fetchRow($query, $params = [], $types = '') {
        $stmt = $this->run_query($query, $params, $types);
        return $this->fetch_one($stmt);
    }

    public function execute($query, $params = [], $types = '') {
        $stmt = $this->run_query($query, $params, $types);
        return $stmt->affected_rows;
    }

    public function getLastInsertId() {
        return $this->connection->insert_id;
    }

    public function __destruct() {
        $this->close_database();
    }

    private function init_database($dbServer, $dbUser, $dbPassword, $dbName){
        
        // Trim credentials to avoid accidental CR/LF from files
        $dbServer = trim($dbServer);
        $dbUser = trim($dbUser);
        $dbPassword = trim($dbPassword);
        $dbName = trim($dbName);
    
        // Attempt to connect and handle errors without exposing sensitive details
        try {
            $this->connection = new \mysqli($dbServer, $dbUser, $dbPassword, $dbName);
        } catch (\mysqli_sql_exception $e) {
            // Log detailed error to server logs for debugging, but show a generic message to the client
            error_log("MariaDB connection error: " . $e->getMessage());
            die("Database connection error. Please contact the administrator.");
        }
    
        if ($this->connection->connect_error) {
            $message= <<<HTML
        Database connection error. Please contact the administrator.
        MariaDB connect_error: {$this->connection->connect_error}
    HTML;
            throw new \Exception($message, 1);
            
        }
    
        // Set character set to utf8mb4
        if (!$this->connection->set_charset("utf8mb4")) {
            error_log("Error loading character set utf8mb4: " . $this->connection->error);
            die("Database configuration error. Please contact the administrator.");
        }
    }

    private function run_query($query, $params = [], $types = ''){
        
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->connection->error . " -- Query: " . $query);
            die("Database query error. Please contact the administrator.");
        }
        if (!empty($params)) {
            if (empty($types)) {
                // Automatically determine types if not provided
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } elseif (is_string($param)) {
                        $types .= 's';
                    } else {
                        $types .= 'b'; // blob and unknown
                    }
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error . " -- Query: " . $query);
            die("Database query execution error. Please contact the administrator.");
        }
        return $stmt;
    }

    private function fetch_all($stmt){
        $result = $stmt->get_result();
        if ($result === false) {
            die("Get result failed: " . $stmt->error);
        }
        return $result->fetch_all(\MYSQLI_ASSOC);
    }

    private function fetch_one($stmt){
        $result = $stmt->get_result();
        if ($result === false) {
            die("Get result failed: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    private function close_database(){
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
}
