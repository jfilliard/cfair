<?php

namespace CFair;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use CFair\Tool\ResetDatabaseTool;
use CFair\Tool\ProcessorTool;

class Tools implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container['reset-database.tool'] = function(Container $container) {
        	return new ResetDatabaseTool(
        		$container['db'],
        		$container['logger']
        	);
        };
        $container['processor.tool'] = function(Container $container) {
        	return new ProcessorTool(
        		$container['db'],
        		$container['logger']
        	);
        };
    }
}
