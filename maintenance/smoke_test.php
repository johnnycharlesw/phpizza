<?php
require __DIR__ . '/init.php';
require __DIR__ . '/includes/PageDatabase.php';

try {
    $db = new PHPizza\MariaDB($dbServer, $dbUser, $dbPassword, $dbName);
    echo "OK\n";
} catch (Throwable $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
