<?php
namespace PHPizza;

class PostgreSQL {
    private $dbServer;
    private $dbUser;
    private $dbPassword;
    private $dbName;
    /** @var \PgSql\Connection|resource */
    private $dbInterface;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName) {
        $this->dbServer = $dbServer;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        // PostgreSQL is a client-server DBMS; connect using a connection string.
        $connStr = sprintf(
            'host=%s dbname=%s user=%s password=%s',
            $dbServer,
            $dbName,
            $dbUser,
            $dbPassword
        );
        $this->dbInterface = @pg_connect($connStr);

        if (!$this->dbInterface) {
            throw new \Exception("Could not connect to PostgreSQL server at $dbServer", 1);
        }
    }

    public function fetchAll($query, $params = [], $types = '') {
        // $types is ignored for PostgreSQL; types are inferred/bound by the driver.
        $result = pg_query_params($this->dbInterface, $query, $params);
        if ($result === false) {
            return [];
        }
        $rows = pg_fetch_all($result);
        return $rows ?: [];
    }

    public function fetchRow($query, $params = [], $types = '') {
        $result = pg_query_params($this->dbInterface, $query, $params);
        if ($result === false) {
            return null;
        }
        $row = pg_fetch_assoc($result);
        return $row === false ? null : $row;
    }

    public function execute($query, $params = [], $types = ''){
        $result = pg_query_params($this->dbInterface, $query, $params);
        if ($result === false) {
            return 0;
        }
        return pg_affected_rows($result);
    }

    public function getLastInsertId() {
        $result = pg_query($this->dbInterface, "SELECT LASTVAL() AS id");
        if ($result === false) {
            return null;
        }
        $row = pg_fetch_assoc($result);
        return $row ? (int)$row['id'] : null;
    }

    public function get_table_exists(string $tableName) {
        $query = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = ?)";
        $result = pg_query_params($this->dbInterface, $query, [$tableName]);
        return $result && pg_num_rows($result) > 0 && pg_fetch_row($result)[0] === true;
    }

    public function __destruct() {
        if ($this->dbInterface) {
            @pg_close($this->dbInterface);
        }
    }
}