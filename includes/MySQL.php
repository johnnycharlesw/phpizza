<?php
namespace PHPizza;


class MySQL {
    private $dbServer;
    private $dbUser;
    private $dbPassword;
    private $dbName;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName) {
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
        global $mysql_connection;
        return $mysql_connection->insert_id;
    }

    public function __destruct() {
        close_database();
    }
}

global $mysql_connection;
function init_database($dbServer, $dbUser, $dbPassword, $dbName){
    global $mysql_connection;
    // Trim credentials to avoid accidental CR/LF from files
    $dbServer = trim($dbServer);
    $dbUser = trim($dbUser);
    $dbPassword = trim($dbPassword);
    $dbName = trim($dbName);

    // Attempt to connect and handle errors without exposing sensitive details
    try {
        $mysql_connection = new \mysqli($dbServer, $dbUser, $dbPassword, $dbName);
    } catch (\mysqli_sql_exception $e) {
        // Log detailed error to server logs for debugging, but show a generic message to the client
        error_log("MySQL connection error: " . $e->getMessage());
        die("Database connection error. Please contact the administrator.");
    }

    if ($mysql_connection->connect_error) {
        $message= <<<HTML
    Database connection error. Please contact the administrator.
    mysql connect_error: {$mysql_connection->connect_error}
HTML;
        throw new \Exception($message, 1);
        
    }

    // Set character set to utf8mb4
    if (!$mysql_connection->set_charset("utf8mb4")) {
        error_log("Error loading character set utf8mb4: " . $mysql_connection->error);
        die("Database configuration error. Please contact the administrator.");
    }
}
function run_query($query, $params = [], $types = ''){
    global $mysql_connection;
    $stmt = $mysql_connection->prepare($query);
    if ($stmt === false) {
        error_log("Prepare failed: " . $mysql_connection->error . " -- Query: " . $query);
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
    global $mysql_connection;
    if ($mysql_connection) {
        $mysql_connection->close();
    }
}


