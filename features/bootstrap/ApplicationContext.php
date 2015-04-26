<?php

use Monolog\Handler\NullHandler;
use CFair\Application;

/**
 * Behat context class.
 */
trait ApplicationContext
{
    private $application;

    /**
     * Initializes context.
     *
     * Every scenario gets it's own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    private function application()
    {
        if ($this->application !== null) {
            return $this->application;
        }
        $this->application = new Application;
        $this->application['monolog'] = $this->application->extend('monolog', function($monolog) {
            $monolog->pushHandler(new NullHandler);
            return $monolog;
        });
        return $this->application;
    }
}
