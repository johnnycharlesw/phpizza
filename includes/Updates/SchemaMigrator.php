<?php
namespace PHPizza\Updates;
use PHPizza\Database\Database;

class SchemaMigrator 
{
    public string $schemaPath;
    private Database $db;

    public function __construct() {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->schemaPath = __DIR__ . '/sql/schema/' . $dbType;
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function migrate(){
        foreach (scandir($this->schemaPath) as $tabledir_path) {
            if (file_exists($tabledir_path . '/update.sql')) {
                $sql = file_get_contents($tabledir_path . '/update.sql');
                $this->db->execute($sql);
            }
        }
    }
}
