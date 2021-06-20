<?php

use Cli\Application;
use Cli\Loader;

require('./library/Loader.php');

try {
    Loader::register(
        [
            '\Cli' => __DIR__ . '/library',
            '\Cli\Controllers' => __DIR__ . '/app/Controllers',
        ],
    );

    (new Application())->run();
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
    echo PHP_EOL;

    echo $e;

    exit(1);
}