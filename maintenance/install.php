<?php
echo "Installing PHPizza...";

foreach ([
    "uploads",
    "config"
] as $folder) {
    mkdir(__DIR__ . "../" . $folder);
}