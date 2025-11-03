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
$sitename = "My Website";
$siteLanguage = "en";
$useSkin=true;
$skinName="PHPizza";
$homepageName="home";
$specialPrefix="PHPizza:";

// Database configuration
$dbServer = "localhost";
$dbUser = "phpizza";
// Either set the plain password here, or leave unset and create passwd.b64 with base64-encoded password.
#$dbPassword = base64_decode(file_get_contents("passwd.b64"));
$dbName = "phpizza";

$dbType = "mariadb"; # Change this if you are using a different DBMS, e.g. "sqlite" or "pgsql"

// Optional: If you want to use a passwd.b64 file instead of $dbPassword inline, uncomment:
// $dbPassword = base64_decode(file_get_contents(__DIR__ . '/passwd.b64'));

// Optional: enable debug mode (do not enable on production)
$debug = false;
