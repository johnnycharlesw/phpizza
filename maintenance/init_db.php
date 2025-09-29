<?php
// Simple initializer that reads config.php and executes sql/create_schema.sql
require_once __DIR__ . "/../config.php";
$schemaFile = __DIR__ . "/../sql/create_schema.sql";
if (!file_exists($schemaFile)) {
    echo "Schema file not found: $schemaFile\n";
    exit(1);
}

$schema = file_get_contents($schemaFile);
if ($schema === false) {
    echo "Failed to read schema file.\n";
    exit(1);
}

// $dbPassword in config.php is base64_decoded already
$mysqli = new mysqli(trim($dbServer), trim($dbUser), trim($dbPassword));
if ($mysqli->connect_error) {
    echo "Connection failed: " . $mysqli->connect_error . "\n";
    exit(1);
}

if ($mysqli->multi_query($schema)) {
    do {
        /* store first result set */
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
    echo "Schema executed successfully.\n";
} else {
    echo "Failed to execute schema: " . $mysqli->error . "\n";
}

$mysqli->close();
