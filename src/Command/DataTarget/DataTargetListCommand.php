<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataTarget;

use App\Command\AbstractCommand;
use App\DataTarget\DataTargetManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DataTargetListCommand extends AbstractCommand
{
    protected static $defaultName = 'datatidy:data-target:list';

    /** @var DataTargetManager */
    private $manager;

    public function __construct(DataTargetManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targets = $this->manager->getDataTargets();

        $table = new Table($output);

        $table->setHeaders([
            'name',
            'description',
            'options',
        ]);

        foreach ($targets as $target) {
            $table->addRow([
                $target['name'],
                $target['description'],
                Yaml::dump($target['options']),
            ]);
        }

        $table->render();
    }
}
