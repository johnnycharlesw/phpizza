<?php
// /includes/serve-legal-warning.php


$decoyMessage = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/private/decoy-script.php');
header('Content-Type: text/php');
echo $decoyMessage;

exit;
?>