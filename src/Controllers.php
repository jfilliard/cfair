<?php

namespace CFair;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use CFair\Controller\ConsumerController;
use CFair\Controller\OverviewController;

class Controllers implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container['consumer.controller'] = function(Container $container) {
            return new ConsumerController(
                $container['consumer.usecase'],
                $container['logger']
            );
        };
        $container['overview.controller'] = function(Container $container) {
            return new OverviewController(
                $container['db'],
                $container['twig']
            );
        };
    }
}
