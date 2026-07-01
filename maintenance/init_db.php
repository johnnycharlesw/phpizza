<?php
global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
echo <<<EOS
PHPizza has switched to a self-installation system and no longer uses manual database initialization. As long as these credentials are valid and the database user can modify the schema, PHPizza will self-install:
Server: $dbServer
User: $dbUser
Password: $dbPassword
Name: $dbName
Type: $dbType 
EOS;