<?php

namespace CFair\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $this->addOption(
            'sleep',
            null,
            InputOption::VALUE_REQUIRED,
            'Time (in seconds) to sleep before checking for new ones when none were found. If set to 0, it will not try to find more jobs',
            0
        );
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of job to process',
            'INF'
        );
        $this->setDescription('Process pending jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sleep = $input->getOption('sleep');
        $limit = $input->getOption('limit');
        if ($limit === 'INF') {
            $limit = INF;
        }
        $this->processortool->process($limit, $sleep);
    }
}
