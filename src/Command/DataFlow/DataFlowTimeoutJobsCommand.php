<?php


namespace App\Command\DataFlow;


use App\Entity\DataFlowJob;
use App\Event\DataFlowJobTimeOutEvent;
use App\Repository\DataFlowJobRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataFlowTimeoutJobsCommand extends Command
{
    protected static $defaultName = 'datatidy:data-flow:timeout-jobs';

    private $eventDispatcher;
    private $dataFlowJobRepository;
    private $jobTimeoutThreshold;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataFlowJobRepository $dataFlowJobRepository, $jobTimeoutThreshold)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataFlowJobRepository = $dataFlowJobRepository;
        $this->jobTimeoutThreshold = $jobTimeoutThreshold;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $threshold = $this->jobTimeoutThreshold * 60;

        $activeJobs = $this->dataFlowJobRepository->findBy(
            [
                'status' => [DataFlowJob::STATUS_RUNNING, DataFlowJob::STATUS_CREATED, DataFlowJob::STATUS_QUEUED]
            ]
        );

        foreach ($activeJobs as $job) {

            $seconds = $now->getTimestamp() - $job->getCreatedAt()->getTimestamp();

            if ($seconds >= $threshold) {

                $job->setStatus(DataFlowJob::STATUS_CANCELLED);
                $this->eventDispatcher->dispatch(new DataFlowJobTimeOutEvent($job));
            }
        }
    }
}
