<?php

# Script to encrypt the database password
# Usage: php encrypt-db-password.php

function encrypt_password(string $password){
    return base64_encode($password); // Will be made actually encrypt later
}

function main(){
    $password=readline("Enter database password: ");
    file_put_contents(__DIR__ . "/../passwd.b64",encrypt_password($password));
}

main();