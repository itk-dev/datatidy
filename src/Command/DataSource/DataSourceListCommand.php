<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataSource;

use App\Command\AbstractCommand;
use App\DataSource\AbstractDataSource;
use App\DataSource\DataSourceManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DataSourceListCommand extends AbstractCommand
{
    protected static $defaultName = 'datatidy:data-source:list';

    /** @var DataSourceManager */
    private $manager;

    public function __construct(DataSourceManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this->addOption('show-options', null, InputOption::VALUE_NONE, 'Show data source options');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $showOptions = $input->getOption('show-options');

        $sources = $this->manager->getDataSources();

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

        /** @var AbstractDataSource $source */
        foreach ($sources as $source) {
            $row = [
                $source['name'],
                $source['description'],
                $source['class'],
            ];
            if ($showOptions) {
                $row[] = Yaml::dump($source['options']);
            }
            $table->addRow($row);
        }

        $table->render();
    }
}
