<?php
namespace PHPizza;

# Add in a class for Percona Server

class PerconaServer extends MySQL {
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName){
        parent::__construct($dbServer, $dbUser, $dbPassword, $dbName);
    }
}