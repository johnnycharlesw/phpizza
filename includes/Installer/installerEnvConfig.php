<?php
/*
  default-config.php
  Copy this file to config.php and edit the values for your environment.

  Notes:
  - The real `config.php` used by the app should define $sitename, $siteLanguage,
    $dbServer, $dbUser, $dbPassword, and $dbName.
  - By default this project expects the DB password to be stored in a base64 encoded
    file called `passwd.b64` and `config.php` decodes it. You can keep using that
    pattern or set $dbPassword directly.
*/

// Site
$sitename = "PHPizza";
$siteLanguage = "en";
$useSkin=true;
$skinName="PHPizza";
$homepageName="Setup";
$specialPrefix="";

// Database configuration

// If the installer database does not exist, create it
if (!file_exists("includes/Installer/phpizza_installer.sqlite3")) {
    $dbServer = "localhost";
    $dbUser = "phpizza";
    $dbPassword = "phpizza";
    $dbName = "includes/Installer/phpizza_installer.sqlite3";
    $dbType = "sqlite"; # Change this if you are using a different DBMS, e.g. "sqlite" or "pgsql"
    $dbInterface = new \PHPizza\Database\SQLite($dbServer, $dbUser, $dbPassword, $dbName);
    $dbInterface->execute(file_get_contents(__DIR__ . '/sql/create_schema_sqlite.sql'));
    $dbInterface->close_database();
}

$dbServer = "localhost";
$dbUser = "phpizza";
// SQLite doesn't use passwords, so set to empty string
$dbPassword = "";
$dbName = "includes/Installer/phpizza_installer.sqlite3";

$dbType = "sqlite"; # Change this if you are using a different DBMS, e.g. "sqlite" or "pgsql"

// Optional: If you want to use a passwd.b64 file instead of $dbPassword inline, uncomment:
// $dbPassword = base64_decode(file_get_contents(__DIR__ . '/passwd.b64'));

// Optional: enable debug mode (do not enable on production)
$debug = false;

$specialPageClassMap = array_merge($specialPageClassMap, [
    "Setup" => \PHPizza\Installer\SpecialPageSetup::class,
]);