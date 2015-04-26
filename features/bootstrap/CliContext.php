<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Gitory\PimpleCli\ServiceCommandServiceProvider;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\StringInput;
use CFair\Commands;

/**
 * Behat context class.
 */
class CliContext implements SnippetAcceptingContext
{
    use ApplicationContext {
        ApplicationContext::application as private acApplication;
    }

    private $console;

    private function application()
    {
        if ($this->application !== null) {
            return $this->application;
        }
        $this->application = $this->acApplication();
        $this->application->register(new ServiceCommandServiceProvider);
        $this->application->register(new Commands);
        return $this->application;
    }

    private function console()
    {
        if ($this->console !== null) {
            return $this->console;
        }
        $this->console = new ConsoleApplication('cfair', 'dev');
        $this->console->addCommands($this->application()['command.resolver']->commands());
        $this->console->setAutoExit(false);
        return $this->console;
    }

    /**
     * @When the message processor pick it
     */
    public function theMessageProcessorPickIt()
    {
        $this->console()->run(new StringInput('process'), new NullOutput);
    }
}
