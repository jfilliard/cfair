<?php

namespace CFair;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use CFair\Command\ResetDatabaseCommand;
use CFair\Command\ProcessCommand;

class Commands implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container['database.reset.command'] = new ResetDatabaseCommand(
        	'database:reset',
        	$container['reset-database.tool']
        );
        $container['process.command'] = new ProcessCommand(
        	'process',
        	$container['processor.tool']
        );
    }
}
