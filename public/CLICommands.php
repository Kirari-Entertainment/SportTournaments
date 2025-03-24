<?php

require_once '/var/www/html/vendor/autoload.php';

if (php_sapi_name() === 'cli') {
    global $argv;

    $module = $argv[1] ?? null;
    $command = $argv[2] ?? null;
    $argv = array_slice($argv, 3);
    $moduleMatched = false;

    \Kirari\Series\CLI::listen();
    \Kirari\Users\CLI::listen();

    if (!$moduleMatched) {
        echo "Unknown module.\n";
    }
    
} else {
    echo "This script can only be run from the command line.\n";
    exit(1);
}