<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataFlow;

use App\Entity\DataFlowJob;
use App\Event\DataFlowJobTimeOutEvent;
use App\Repository\DataFlowJobRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataFlowTimeoutJobsCommand extends Command
{
    protected static $defaultName = 'datatidy:data-flow:timeout-jobs';

    private $eventDispatcher;
    private $dataFlowJobRepository;
    private $jobTimeoutThreshold;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataFlowJobRepository $dataFlowJobRepository)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataFlowJobRepository = $dataFlowJobRepository;
    }

    public function configure()
    {
        $this->addOption('timeout-threshold', 'tt', InputOption::VALUE_OPTIONAL, 'In minutes how long to wait before a non-complete job should timeout', 30);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $timeoutThreshold = $input->getOption('timeout-threshold');
        if (!\is_int((int) $timeoutThreshold)) {
            throw new \InvalidArgumentException('timeout-threshold can only be an integer value.');
        }

        $now = new \DateTime();
        $threshold = $timeoutThreshold * 60;

        $activeJobs = $this->dataFlowJobRepository->findBy(
            [
                'status' => [DataFlowJob::STATUS_RUNNING, DataFlowJob::STATUS_CREATED, DataFlowJob::STATUS_QUEUED],
            ]
        );

        foreach ($activeJobs as $job) {
            $seconds = $now->getTimestamp() - $job->getCreatedAt()->getTimestamp();

            if ($seconds >= $threshold) {
                $job->setStatus(DataFlowJob::STATUS_CANCELLED);
                $this->eventDispatcher->dispatch(new DataFlowJobTimeOutEvent($job, $timeoutThreshold));
            }
        }
    }
}
