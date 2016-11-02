<?php
$dir = null;
if (realpath(__DIR__ . '/../vendor/')) {
    $dir = realpath(__DIR__ . '/../vendor/');
} elseif (realpath(__DIR__ . '/../../../../vendor')) {
    // package required in another composer.json
    $dir = realpath(__DIR__ . '/../../../../vendor');
} else {
    echo 'rikby/merge-xml: It looks like there are no installed required packages.' . PHP_EOL;
    echo 'Please run "composer install" within commithook directory.';
    exit(1);
}

/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require $dir . '/autoload.php';

set_error_handler('\PreCommit\ErrorHandler::handleError');

return $autoloader;
