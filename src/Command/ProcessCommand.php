<?php

namespace CFair\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CFair\Tool\ProcessorTool;

class ProcessCommand extends Command
{
	private $processortool;

	public function __construct($name, ProcessorTool $processortool)
	{
		parent::__construct($name);

		$this->processortool = $processortool;
	}

    protected function configure()
    {
        $this->setDescription('Process pending jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$this->processortool->processAll();
    }
}
