<?php
require __DIR__ . '/init.php';
global $specialPrefix;
echo json_encode(
    [
        'specialPrefix' => $specialPrefix
    ]
);