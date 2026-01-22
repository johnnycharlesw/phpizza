<?php
// Apply the project's SQLite schema to the installer SQLite DB
$projectRoot = realpath(__DIR__ . '/..');
$schemaPath = $projectRoot . '/sql/create_schema_sqlite.sql';
$dbPath = $projectRoot . '/includes/Installer/phpizza_installer.sqlite3';

if (!file_exists($schemaPath)) {
    fwrite(STDERR, "Schema file not found: $schemaPath\n");
    exit(1);
}

$schema = file_get_contents($schemaPath);
if ($schema === false) {
    fwrite(STDERR, "Failed to read schema file: $schemaPath\n");
    exit(1);
}

try {
    $dir = dirname($dbPath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            fwrite(STDERR, "Failed to create directory for DB: $dir\n");
            exit(1);
        }
    }

    $db = new SQLite3($dbPath);
    $ok = $db->exec($schema);
    if ($ok === false) {
        fwrite(STDERR, "Failed to execute schema: " . $db->lastErrorMsg() . "\n");
        exit(1);
    }
    echo "Schema applied to $dbPath\n";
    $db->close();
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "Exception: " . $e->getMessage() . "\n");
    exit(1);
}
