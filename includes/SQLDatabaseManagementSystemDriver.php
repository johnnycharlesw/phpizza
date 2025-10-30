<?php
namespace PHPizza;
# SQL DBMS Driver interface
interface SQLDatabaseManagementSystemDriver {
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName);
    public function execute($query, $params = [], $types = '');
    public function fetchAll($query, $params = [], $types = '');
    public function fetchRow($query, $params = [], $types = '');
    public function getLastInsertId();
    public function __destruct();
}