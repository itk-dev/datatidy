<?php


namespace App\MessageHandler;


use App\DataFlow\DataFlowManager;
use App\DataFlow\DataFlowRunResult;
use App\Entity\DataFlow;
use App\Message\RunDataFlowMessage;
use App\Repository\DataFlowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RunDataFlowMessageHandler implements MessageHandlerInterface
{
    private $dataFlowManager;
    private $dataFlowRepository;
    private $entityManager;
    private $logger;

    public function __construct(DataFlowManager $dataFlowManager, DataFlowRepository $dataFlowRepository, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->dataFlowManager = $dataFlowManager;
        $this->dataFlowRepository = $dataFlowRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function __invoke(RunDataFlowMessage $message)
    {
        /** @var DataFlow $dataFlow */
        $dataFlow = $this->dataFlowRepository->find($message->getDataFlowId());

        /** @var DataFlowRunResult $result */
        $result = $this->dataFlowManager->run($dataFlow);

        $logMessage = $result->isSuccess()
            ? sprintf('%s:%s data flow ran successfully', $dataFlow->getId(), $dataFlow->getName())
            : sprintf('%s:%s data flow was erronous: %s', $dataFlow->getId(), $dataFlow->getName(), \implode(',', $result->getExceptions()))
        ;

        $this->logger->info($logMessage);

        $dataFlow->setLastRunAt(new \DateTime());
        $this->entityManager->persist($dataFlow);
        $this->entityManager->flush();
    }
}
