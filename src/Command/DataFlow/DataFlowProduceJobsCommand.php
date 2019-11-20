<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command\DataFlow;

use App\Entity\DataFlow;
use App\Entity\DataFlowJob;
use App\Event\DataFlowJobCreatedEvent;
use App\Event\DataFlowJobQueuedEvent;
use App\Message\RunDataFlowJobMessage;
use App\Repository\DataFlowJobRepository;
use App\Repository\DataFlowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DataFlowProduceJobsCommand extends Command
{
    protected static $defaultName = 'datatidy:data-flow:produce-jobs';

    private $dataFlowRepository;
    private $dataFlowJobRepository;
    private $entityManager;
    private $eventDispatcher;
    private $messageBus;

    public function __construct(
        DataFlowRepository $dataFlowRepository,
        DataFlowJobRepository $dataFlowJobRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->dataFlowRepository = $dataFlowRepository;
        $this->dataFlowJobRepository = $dataFlowJobRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageBus = $messageBus;
    }

    protected function configure()
    {
        $this->setDescription('Produces Data Flow jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataFlowCandidates = $this->dataFlowRepository->findBy([
            'enabled' => true,
        ]);

        $dataFlowsToRun = $this->getDataFlowsToRun($dataFlowCandidates);

        foreach ($dataFlowsToRun as $dataFlow) {
            // Job creation
            $job = new DataFlowJob();
            $job->setStatus(DataFlowJob::STATUS_CREATED);
            $job->setDataFlow($dataFlow);
            $this->entityManager->persist($dataFlow);

            $dataFlow->setLastRunAt(new \DateTime());
            $this->entityManager->persist($job);

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new DataFlowJobCreatedEvent($job));

            // Delivery of job to queue.
            // This is a different from the job creation and could be helpful in error handling.
            $this->messageBus->dispatch(
                new RunDataFlowJobMessage($job->getId())
            );
            $job->setStatus(DataFlowJob::STATUS_QUEUED);
            $this->entityManager->persist($job);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new DataFlowJobQueuedEvent($job));
        }
    }

    /**
     * @return DataFlow[]
     *
     * @throws \Exception
     */
    private function getDataFlowsToRun(array $dataFlowCandidates): array
    {
        $now = new \DateTime();

        return array_filter($dataFlowCandidates, function (DataFlow $dataFlow) use ($now) {
            // If data flow hasn't run yet at all is should do it now
            if (empty($dataFlow->getLastRunAt())) {
                return true;
            }

            // If there already is an active job (not completed or failed jobs), if so we should not schedule a new job
            $activeJobs = $this->dataFlowJobRepository->findActiveJobsByDataFlow($dataFlow);
            if (!empty($activeJobs)) {
                return false;
            }

            // Difference in seconds between now and last time the data flow ran
            $seconds = $now->getTimestamp() - $dataFlow->getLastRunAt()->getTimestamp();

            if ($dataFlow->getFrequency() < $seconds) {
                return true;
            }

            return false;
        });
    }
}
