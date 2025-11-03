<?php
namespace PHPizza;

# Add in a class for Percona Server

class PerconaServer implements SQLDatabaseManagementSystemDriver {

    private $connection;
    private $dbServer;
    private $dbUser;
    private $dbPassword;
    private $dbName;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName){
        $this->__construct($dbServer, $dbUser, $dbPassword, $dbName);
    }

    public function ___construct($dbServer, $dbUser, $dbPassword, $dbName) {
        $this->dbServer = $dbServer;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        init_database($this->dbServer, $this->dbUser, $this->dbPassword, $this->dbName);
    }

    public function fetchAll($query, $params = [], $types = '') {
        $stmt = run_query($query, $params, $types);
        return fetch_all($stmt);
    }

    public function fetchRow($query, $params = [], $types = '') {
        $stmt = run_query($query, $params, $types);
        return fetch_one($stmt);
    }

    public function execute($query, $params = [], $types = '') {
        $stmt = run_query($query, $params, $types);
        return $stmt->affected_rows;
    }

    public function getLastInsertId() {
        
        return $this->connection->insert_id;
    }
    
    public function get_table_exists(string $tableName){
        $query = "SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";
        $stmt = $this->fetchAll($query, [$this->dbName, $tableName]);
        return !empty($stmt);
    }

    public function __destruct() {
        close_database();
    }
    function init_database($dbServer, $dbUser, $dbPassword, $dbName){
        
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
            error_log("Percona Server connection error: " . $e->getMessage());
            die("Database connection error. Please contact the administrator.");
        }

        if ($this->connection->connect_error) {
            $message= <<<HTML
        Database connection error. Please contact the administrator.
        Percona Server connect_error: {$this->connection->connect_error}
HTML;
            throw new \Exception($message, 1);
            
        }

        // Set character set to utf8mb4
        if (!$this->connection->set_charset("utf8mb4")) {
            error_log("Error loading character set utf8mb4: " . $this->connection->error);
            die("Database configuration error. Please contact the administrator.");
        }
    }
    function run_query($query, $params = [], $types = ''){
        
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

    function fetch_all($stmt){
        $result = $stmt->get_result();
        if ($result === false) {
            die("Get result failed: " . $stmt->error);
        }
        return $result->fetch_all(\MYSQLI_ASSOC);
    }

    function fetch_one($stmt){
        $result = $stmt->get_result();
        if ($result === false) {
            die("Get result failed: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    function close_database(){
        
        if ($this->connection) {
            $this->connection->close();
        }
    }



}

