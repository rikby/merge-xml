<?php
/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require 'autoload-init.php';

use Rikby\MergeXml\Command\MergeCommand;
use Symfony\Component\Console\Application;

if (!isset($_SERVER['argv'][1]) || $_SERVER['argv'][1] !== 'merge') {
    $file = array_shift($_SERVER['argv']);
    array_unshift($_SERVER['argv'], $file, 'merge');
}

$command = new MergeCommand();
$application = new Application();
$application->add($command);
$application->run();


