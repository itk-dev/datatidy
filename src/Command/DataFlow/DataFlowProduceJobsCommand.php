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
    private $entityManager;
    private $eventDispatcher;
    private $messageBus;

    public function __construct(
        DataFlowRepository $dataFlowRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->dataFlowRepository = $dataFlowRepository;
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

        /** @var DataFlow $dataFlow */
        foreach ($dataFlowsToRun as $dataFlow) {
            $job = new DataFlowJob();
            $job->setStatus(DataFlowJob::STATUS_CREATED);
            $job->setDataFlow($dataFlow);

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new DataFlowJobCreatedEvent($job));
            $this->messageBus->dispatch(
                new RunDataFlowJobMessage($job->getId())
            );

            $job->setStatus(DataFlowJob::STATUS_QUEUED);
            $this->entityManager->persist($job);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new DataFlowJobQueuedEvent($job));
        }
    }

    private function getDataFlowsToRun(array $dataFlowCandidates): array
    {
        return array_filter($dataFlowCandidates, function ($dataFlow) {
            /** @var DataFlow $dataFlow */

            // If data flow hasn't run yet at all is should do it now
            if (empty($dataFlow->getLastRunAt())) {
                return true;
            }

            // Difference in seconds between now and last time the data flow ran
            $seconds = (new \DateTime())->getTimestamp() - $dataFlow->getLastRunAt()->getTimestamp();

            if ($dataFlow->getFrequency() < $seconds) {
                return true;
            }

            return false;
        });
    }
}
