<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\Flow;

use App\DataFlow\DataFlowManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class RunFlowCommand extends Command
{
    protected static $defaultName = 'datatidy:flow:run';

    /** @var DataFlowManager */
    private $manager;

    public function __construct(DataFlowManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->addArgument('flow', InputArgument::REQUIRED, 'Id of the data flow to run')
            ->addOption('publish', null, InputOption::VALUE_NONE, 'If set, send result to data targets')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'If set, write result to console');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);
        $this->manager->setLogger($logger);

        $id = $input->getArgument('flow');
        $flow = $this->manager->getDataFlow($id);
        if (null === $flow) {
            throw new InvalidArgumentException(sprintf('Invalid data flow: %s', $id));
        }
        $options = [
            'publish' => $input->getOption('publish'),
        ];

        $result = $this->manager->run($flow, $options);

        if ($input->getOption('dump')) {
            foreach ($result as $index => $dataSet) {
                $table = new Table($output);
                $table->setHeaderTitle(sprintf('#%d: %s', $index + 1, $dataSet->getName()));
                $first = true;
                foreach ($dataSet->rows() as $row) {
                    if ($first) {
                        $table->setHeaders(array_keys($row));
                        $first = false;
                    }
                    $table->addRow(array_map(static function ($value) {
                        return is_scalar($value) ? $value : json_encode($value);
                    }, $row));
                }
                $table->render();
            }
        }
    }
}
