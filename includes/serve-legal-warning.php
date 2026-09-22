<?php
// /includes/serve-legal-warning.php

$id = rand(0, 4);
$decoyMessage = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/private/decoy-script-'.$id.'.php');
header('Content-Type: text/php');
echo $decoyMessage;

exit;
?>