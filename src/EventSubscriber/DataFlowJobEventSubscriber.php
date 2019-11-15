<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\EventSubscriber;

use App\Entity\DataFlowJob;
use App\Entity\DataFlowJobLogEntry;
use App\Event\DataFlowJobCompletedEvent;
use App\Event\DataFlowJobCreatedEvent;
use App\Event\DataFlowJobFailedEvent;
use App\Event\DataFlowJobQueuedEvent;
use App\Event\DataFlowJobRunningEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataFlowJobEventSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DataFlowJobCreatedEvent::class => 'onCreated',
            DataFlowJobQueuedEvent::class => 'onQueued',
            DataFlowJobFailedEvent::class => 'onFailed',
            DataFlowJobCompletedEvent::class => 'onCompleted',
            DataFlowJobRunningEvent::class => 'onRunning',
        ];
    }

    public function onCreated(DataFlowJobCreatedEvent $event)
    {
        $this->log(
            $event->getJob(),
            DataFlowJobLogEntry::LEVEL_INFO,
            $this->translate('Job created')
        );
    }

    public function onQueued(DataFlowJobQueuedEvent $event)
    {
        $this->log(
            $event->getJob(),
            DataFlowJobLogEntry::LEVEL_INFO,
            $this->translate('Job queued')
        );
    }

    public function onFailed(DataFlowJobFailedEvent $event)
    {
        $errorMessage = $event->getResult()->getException()->getMessage();

        $this->log(
            $event->getJob(),
            DataFlowJobLogEntry::LEVEL_ERROR,
            $this->translate('Job failed: %error%', ['error' => $errorMessage])
        );
    }

    public function onCompleted(DataFlowJobCompletedEvent $event)
    {
        $this->log(
            $event->getJob(),
            DataFlowJobLogEntry::LEVEL_INFO,
            $this->translate('Job completed')
        );
    }

    public function onRunning(DataFlowJobRunningEvent $event)
    {
        $this->log(
            $event->getJob(),
            DataFlowJobLogEntry::LEVEL_INFO,
            $this->translate('Job running')
        );
    }

    private function log(DataFlowJob $job, string $level, string $message)
    {
        $logEntry = new DataFlowJobLogEntry();
        $logEntry->setLevel($level);
        $logEntry->setMessage($message);
        $logEntry->setJob($job);

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }

    private function translate(string $id, array $parameters = [], string $domain = 'data_flow_job'): string
    {
        return $this->translator->trans($id, $parameters, $domain);
    }
}
