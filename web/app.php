<?php

include_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use CFair\Application;
use CFair\Controllers;
use CFair\Routes;

$app = new Application;

$app->register(new TwigServiceProvider, [
    'twig.path' => __DIR__.'/../views',
]);
$app->register(new ServiceControllerServiceProvider);
$app->register(new Controllers);
$app->mount('/', new Routes);

$app->run();
