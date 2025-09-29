<?php

if (php_sapi_name() !== "cli") {
    die("This is the CLI. It should only be run from the command line.");
}

include 'index.php';