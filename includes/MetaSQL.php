<?php
namespace PHPizza;

# Add in a class for Facebook, Inc.'s corporate fork of MySQL

// This subclass exists to handle quirks or future customizations
// specific to Meta's MySQL fork: https://github.com/facebook/mysql-8.0

class MetaSQL extends MySQL
{
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName){
        parent::__construct($dbServer, $dbUser, $dbPassword, $dbName);
    }
}

