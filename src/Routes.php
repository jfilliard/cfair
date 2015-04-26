<?php

namespace CFair;

use Silex\Application as SilexApplication;
use Silex\Api\ControllerProviderInterface;

class Routes implements ControllerProviderInterface {
    public function connect(SilexApplication $app)
    {
        $routes = $app['controllers_factory'];

        $routes->post('/consume', 'consumer.controller:exec')->bind('consume');
        $routes->get('/', 'overview.controller:index')->bind('overview');
        $routes->get('/reset', 'reset.controller:exec')->bind('reset');

        return $routes;
    }
}
