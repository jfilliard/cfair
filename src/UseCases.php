<?php

namespace CFair;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use CFair\UseCase\Database\DatabaseConsumerUseCase;

class UseCases implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container['consumer.usecase'] = function(Container $container) {
            return new DatabaseConsumerUseCase(
                $container['db'],
                $container['logger']
            );
        };
    }
}
