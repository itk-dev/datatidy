<?php


namespace App\MessageHandler;


use App\DataFlow\DataFlowManager;
use App\DataFlow\DataFlowRunResult;
use App\Entity\DataFlowJob;
use App\Event\DataFlowJobCompletedEvent;
use App\Event\DataFlowJobFailedEvent;
use App\Event\DataFlowJobRunningEvent;
use App\Message\RunDataFlowJobMessage;
use App\Repository\DataFlowJobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RunDataFlowJobMessageHandler implements MessageHandlerInterface
{
    private $dataFlowManager;
    private $dataFlowJobRepository;
    private $entityManager;
    private $eventDispatcher;

    public function __construct(
        DataFlowManager $dataFlowManager,
        DataFlowJobRepository $dataFlowJobRepository,
        EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher
    )
    {
        $this->dataFlowManager = $dataFlowManager;
        $this->dataFlowJobRepository = $dataFlowJobRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(RunDataFlowJobMessage $message)
    {
        /** @var DataFlowJob $job */
        $job = $this->dataFlowJobRepository->find($message->getDataFlowJobId());
        $dataFlow = $job->getDataFlow();

        /** @var DataFlowRunResult $result */
        $result = $this->dataFlowManager->run($dataFlow);

        $job->setStartedAt(new \DateTime());
        $job->setStatus(DataFlowJob::STATUS_RUNNING);
        $this->entityManager->persist($job);
        $this->entityManager->flush($job);

        $this->eventDispatcher->dispatch(
            new DataFlowJobRunningEvent($job)
        );

        if (!$result->isSuccess()) {
            $job->setStatus(DataFlowJob::STATUS_FAILED);
            $this->eventDispatcher->dispatch(
                new DataFlowJobFailedEvent($job, $result)
            );
        }

        if ($result->isComplete()) {
            $job->setStatus(DataFlowJob::STATUS_COMPLETED);
            $this->eventDispatcher->dispatch(
                new DataFlowJobCompletedEvent($job)
            );
        }

        $this->entityManager->persist($job);

        $dataFlow->setLastRunAt(new \DateTime());
        $this->entityManager->persist($dataFlow);
        $this->entityManager->flush();
    }
}
