<?php

include_once __DIR__.'/../vendor/autoload.php';

use Gitory\PimpleCli\ServiceCommandServiceProvider;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\ConsoleOutput;
use CFair\Application;
use CFair\Commands;

$app = new Application;

$console = new ConsoleApplication('cfair', 'dev');

$output = new ConsoleOutput();
$app['monolog']->pushHandler(new ConsoleHandler($output));

$app->register(new ServiceCommandServiceProvider);
$app->register(new Commands);

$console->addCommands($app['command.resolver']->commands());
$console->run(null, $output);
