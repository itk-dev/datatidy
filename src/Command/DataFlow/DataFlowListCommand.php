<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataFlow;

use App\Command\AbstractCommand;
use App\DataFlow\DataFlowManager;
use App\Entity\DataFlow;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataFlowListCommand extends AbstractCommand
{
    protected static $defaultName = 'datatidy:data-flow:list';

    /** @var DataFlowManager */
    private $manager;

    public function __construct(DataFlowManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $flows = $this->manager->getDataFlows();

        $table = new Table($output);

        $table->setHeaders([
            'id',
            'name',
            'description',
            'enabled',
            '#transforms',
            '#targets',
            'last run at',
        ]);
        $this->setAlignment($table, [0, 3, 4], 'right');

        /** @var DataFlow $flow */
        foreach ($flows as $flow) {
            $table->addRow([
                $flow->getId(),
                $flow->getName(),
                $flow->getDescription(),
                $flow->getEnabled() ? '✓' : '',
                $flow->getTransforms()->count(),
                $flow->getDataTargets()->count(),
                $flow->getLastRunAt() ? $flow->getLastRunAt()->format(\DateTime::ATOM) : null,
            ]);
        }

        $table->render();
    }
}
