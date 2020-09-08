<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFlow;

use App\DataSet\DataSet;
use App\DataSet\DataSetColumnList;
use App\DataSet\DataSetManager;
use App\DataSource\DataSourceManager;
use App\DataTarget\DataTargetManager;
use App\DataTransformer\DataTransformerManager;
use App\Entity\DataFlow;
use App\Repository\DataFlowRepository;
use App\Traits\LogTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataFlowManager
{
    use LogTrait;

    /** @var DataSourceManager */
    protected $dataSourceManager;

    /** @var DataSetManager */
    protected $dataSetManager;

    /** @var DataTransformerManager */
    protected $transformerManager;

    /** @var DataTargetManager */
    protected $dataTargetManager;

    /** @var DataFlowRepository */
    protected $repository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        DataSourceManager $dataSourceManager,
        DataSetManager $dataSetManager,
        DataTransformerManager $transformerManager,
        DataTargetManager $dataTargetManager,
        DataFlowRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->dataSourceManager = $dataSourceManager;
        $this->dataSetManager = $dataSetManager;
        $this->transformerManager = $transformerManager;
        $this->dataTargetManager = $dataTargetManager;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->logger = new NullLogger();
    }

    /**
     * @return DataFlow[]
     */
    public function getDataFlows(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Get a flow by id or name.
     *
     * If a name is specified
     */
    public function getDataFlow(string $id): ?DataFlow
    {
        $flow = $this->repository->find($id);

        if (null !== $flow) {
            return $flow;
        }

        $flows = $this->repository->findByName($id);
        if (1 < \count($flows)) {
            throw new \RuntimeException(sprintf('Ambiguous flow name: %s', $id));
        }

        return reset($flows) ?: null;
    }

    public function getDataSourceManager(): DataSourceManager
    {
        return $this->dataSourceManager;
    }

    public function runColumns(DataFlow $dataFlow, array $options = []): DataSetColumnList
    {
        $options['publish'] = false;

        // @TODO: We should compute columns using only AbstractDataTransformer::transformColumns here.
        $result = $this->run($dataFlow, $options);

        return $result->isSuccess() ? $result->getTransformResult(-1)->getColumns() : new DataSetColumnList();
    }

    public function run(DataFlow $dataFlow, array $options = []): DataFlowRunResult
    {
        $options = $this->resolveRunOptions($options);
        $result = new DataFlowRunResult($dataFlow, $options);

        try {
            $dataSet = $this->getDataSet($dataFlow);
        } catch (\Exception $exception) {
            $result->addTransformException($exception);

            return $result;
        }

        $result->addDataSet($dataSet);
        $numberOfSteps = $options['number_of_steps'] ?? PHP_INT_MAX;
        $transforms = $dataFlow->getTransforms();
        foreach ($transforms as $index => $transform) {
            if ($index >= $numberOfSteps) {
                break;
            }
            try {
                $transformer = $this->transformerManager->getTransformer(
                    $transform->getTransformer(),
                    $transform->getTransformerOptions()
                );
                $dataSet = $transformer->transform($dataSet)->setTransform($transform);
                $result->addDataSet($dataSet);
            } catch (\Exception $exception) {
                $result->setFailedTransform($transform);
                $result->addTransformException($exception);
                // It does not make sense to continue after an exception.
                break;
            }
        }

        // Lookahead.
        if ($result->isSuccess() && $numberOfSteps < $transforms->count()) {
            for ($index = $numberOfSteps; $index < $transforms->count(); ++$index) {
                $transform = $transforms[$index];
                try {
                    $transformer = $this->transformerManager->getTransformer(
                        $transform->getTransformer(),
                        $transform->getTransformerOptions()
                    );
                    $dataSet = $transformer->transform($dataSet)->setTransform($transform);
                    $result->addLookaheadResult($dataSet);
                } catch (\Exception $exception) {
                    // Only store the first failed transform and exception.
                    if (null === $result->getFailedTransform()) {
                        $result->setFailedTransform($transform);
                        $result->setLookaheadException($exception);
                    }
                }
            }
        }

        // Publish result only if all transforms ran successfully.
        if ($options['publish'] && $result->isComplete() && $result->isSuccess()) {
            $dataSet = $result->getLastTransformResult();
            $this->publish($result, $dataSet, $dataFlow->getDataTargets());
            if ($result->isPublished()) {
                $dataFlow->setLastRunAt(new \DateTime());
                $this->entityManager->persist($dataFlow);
                $this->entityManager->flush();
            }
        }

        return $result;
    }

    /**
     * Publish final result to all data targets defined on the data flow.
     */
    private function publish(DataFlowRunResult $result, DataSet $dataSet, Collection $dataTargets)
    {
        $rows = $dataSet->getRows();
        $this->dataTargetManager->setLogger($this->logger);
        foreach ($dataTargets as $dataTarget) {
            try {
                $this->debug(sprintf('publish: %s', $dataTarget));
                $target = $this->dataTargetManager->getDataTarget(
                    $dataTarget->getDataTarget(),
                    $dataTarget->getDataTargetOptions()
                );
                $target->setLogger($this->logger);
                $data = $dataTarget->getData() ?? [];
                $target->publish($rows, $dataSet->getColumns(), $data);
                $dataTarget->setData($data);
                $this->entityManager->persist($dataTarget);
                $result->addPublishResult(true);
            } catch (\Exception $exception) {
                $result->addPublishException($exception);
            }
        }
        $result->setPublished(true);
        $this->entityManager->flush();
    }

    /**
     * Get dataSets indexed by id.
     *
     * @return DataSet
     */
    protected function getDataSet(DataFlow $dataFlow)
    {
        $data = $this->dataSourceManager->getData($dataFlow->getDataSource());
        $dataSet = $this->dataSetManager->createDataSetFromData($dataFlow->getId(), $data);

        return $dataSet;
    }

    private function resolveRunOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureRunOptions($resolver);

        return $resolver->resolve($options);
    }

    private function configureRunOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'number_of_steps' => null,
            'publish' => false,
        ]);
    }
}
