<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Behat context class.
 */
class DomainContext implements SnippetAcceptingContext
{
    use ApplicationContext;

    /**
     * @When I post a message to the consumer
     */
    public function iPostAMessageToTheConsumer(PyStringNode $payload)
    {
        $this->application()['consumer.usecase']->consume(json_decode($payload, true));
    }

    /**
     * @When the message processor pick it
     */
    public function theMessageProcessorPickIt()
    {
        $this->application()['processor.tool']->processAll();
    }
}
