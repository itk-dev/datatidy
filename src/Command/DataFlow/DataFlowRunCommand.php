<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataFlow;

use App\DataFlow\DataFlowManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class DataFlowRunCommand extends Command
{
    protected static $defaultName = 'datatidy:data-flow:run';

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
            ->addArgument('flow', InputArgument::REQUIRED, 'Id or name of the data flow to run')
            ->addOption('publish', null, InputOption::VALUE_NONE, 'If set, send result to data targets')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'If set, write result to console')
            ->addOption('show-data-transformer-options', null, InputOption::VALUE_NONE)
            ->addOption('throw-exceptions', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $showOptions = $input->getOption('show-data-transformer-options');

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

        $io->section('Data flow');

        $io->definitionList(
            ['Name' => $flow->getName()],
            ['Complete?' => $result->isComplete() ? 'yes' : 'no'],
            ['Exception?' => $result->hasTransformException() ? 'yes' : 'no'],
            ['Published?' => $result->isPublished() ? 'yes' : 'no'],
            ['Publish exception?' => $result->hasPublishException() ? 'yes' : 'no']
        );

        if ($output->isVerbose()) {
            $io->section('Result');
            $headers = [
                'index',
                'result type',
                'result details',
            ];
            if ($showOptions) {
                $headers[] = 'transformer options';
            }
            $rows = [];
            foreach ($result->getTransformResults() as $index => $dataSet) {
                $row = [
                    $index,
                    \get_class($dataSet),
                ];

                if ($dataSet->getTransform()) {
                    $row[] = $dataSet->getTransform()->getTransformer();
                    if ($showOptions) {
                        $row[] = Yaml::dump($dataSet->getTransform()->getTransformerOptions());
                    }
                }

                $rows[] = $row;
            }

            $io->table($headers, $rows);
        }

        $exceptions = array_merge($result->getTransformExceptions()->toArray(), $result->getPublishExceptions()->toArray());
        foreach ($exceptions as $exception) {
            if ($input->getOption('throw-exceptions')) {
                throw $exception;
            }
            $output->writeln(sprintf('%s: %s', \get_class($exception), $exception->getMessage()));
        }

        if ($input->getOption('dump')) {
            foreach ($result->getTransformResults() as $index => $dataSet) {
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
