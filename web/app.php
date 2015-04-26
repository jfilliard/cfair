<?php

include_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\ServiceControllerServiceProvider;
use CFair\Application;
use CFair\Controllers;
use CFair\Routes;

$app = new Application;

$app->register(new ServiceControllerServiceProvider);
$app->register(new Controllers);
$app->mount('/', new Routes);

$app->run();
