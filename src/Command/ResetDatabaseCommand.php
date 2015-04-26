<?php

namespace CFair\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CFair\Tool\ResetDatabaseTool;

class ResetDatabaseCommand extends Command
{
	private $resetDatabaseTool;

	public function __construct($name, ResetDatabaseTool $resetDatabaseTool)
	{
		parent::__construct($name);

		$this->resetDatabaseTool = $resetDatabaseTool;
	}

    protected function configure()
    {
        $this->setDescription('Reset database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$this->resetDatabaseTool->exec();
    }
}
