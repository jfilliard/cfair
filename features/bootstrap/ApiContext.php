<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use CFair\Controllers;
use CFair\Routes;

/**
 * Behat context class.
 */
class ApiContext implements SnippetAcceptingContext
{
    use ApplicationContext {
        ApplicationContext::application as private acApplication;
    }

    private function application()
    {
        if ($this->application !== null) {
            return $this->application;
        }
        $this->application = $this->acApplication();
        $this->application->register(new ServiceControllerServiceProvider);
        $this->application->register(new Controllers);
        $this->application->mount('/', new Routes);
        return $this->application;
    }

    /**
     * @When I post a message to the consumer
     */
    public function iPostAMessageToTheConsumer(PyStringNode $payload)
    {
        $request = Request::create('/consume', 'POST', [], [], [], [], (string)$payload);
        $this->application()->handle($request);
    }
}
