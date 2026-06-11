<?php
// /includes/serve-legal-warning.php

if (preg_match('/includes/', $_SERVER["REQUEST_URI"])) {
    $decoyMessage = file_get_contents(__DIR__ . '/../private/decoy-script.php');
    header('Content-Type: text/php');
    die($decoyMessage);
} else {
    return;
}

