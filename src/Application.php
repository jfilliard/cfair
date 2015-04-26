<?php

namespace CFair;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Pimple\Container;
use Silex\Application as SilexApplication;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

class Application extends SilexApplication {
    public function __construct(Array $values = [])
    {
        $defaults = [
            'debug' => strtolower(getenv('APPLICATION_DEBUG')) === 'true',
            'app.name' => 'cfair',
        ];

        parent::__construct(array_merge($defaults, $values));

        if ($this['debug']) {
            $this->register(new \WhoopsSilex\WhoopsServiceProvider);
        }

        $this->register(new UseCases);
        $this->register(new Tools);

        $this->register(new MonologServiceProvider);
        $this['monolog.handler'] = function(Container $container) {
            $handler = new ErrorLogHandler;
            $handler->pushProcessor(new PsrLogMessageProcessor);
            return $handler;
        };
        $this['monolog.name'] = $this['app.name'];

        $this->register(new DoctrineServiceProvider, [
            'db.options' => [
                'driver'   => 'pdo_mysql',
                'host'     => 'db',
                'user'     => 'cfair',
                'password' => 'cfair',
                'dbname'   => 'cfair',
            ],
        ]);
    }
}
