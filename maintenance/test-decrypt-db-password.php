<?php

# Script to encrypt the database password
# Usage: php encrypt-db-password.php



function main(){
    echo base64_decode(file_get_contents(__DIR__ . "/../passwd.b64"));
}

main();