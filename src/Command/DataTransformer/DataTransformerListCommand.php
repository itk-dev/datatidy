<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataTransformer;

use App\Command\AbstractCommand;
use App\DataTarget\AbstractDataTarget;
use App\DataTransformer\DataTransformerManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DataTransformerListCommand extends AbstractCommand
{
    protected static $defaultName = 'datatidy:data-transformer:list';

    /** @var DataTransformerManager */
    private $manager;

    public function __construct(DataTransformerManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this->addOption('show-options', null, InputOption::VALUE_NONE, 'Show transformer options');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $showOptions = $input->getOption('show-options');

        $transformers = $this->manager->getTransformers();

        $table = new Table($output);

        $headers = [
            'name',
            'description',
            'class',
        ];
        if ($showOptions) {
            $headers[] = 'options';
        }
        $table->setHeaders($headers);

        /** @var AbstractDataTarget $transformer */
        foreach ($transformers as $transformer) {
            $row = [
                $transformer['name'],
                $transformer['description'],
                $transformer['class'],
            ];
            if ($showOptions) {
                $row[] = Yaml::dump($transformer['options']);
            }
            $table->addRow($row);
        }

        $table->render();
    }
}