<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFlow;

use App\DataSet\DataSet;
use App\DataSet\DataSetManager;
use App\DataSource\DataSourceManager;
use App\DataTarget\DataTargetManager;
use App\DataTransformer\DataTransformerManager;
use App\Entity\DataFlow;
use App\Entity\DataTransform;
use App\Repository\DataFlowRepository;
use App\Traits\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function runColumns(DataFlow $dataFlow, array $options = []): ArrayCollection
    {
        $options = $this->resolveRunOptions($options);
        $dataSet = $this->getDataSet($dataFlow);

        $columns = $dataSet->getColumns();
        $numberOfSteps = $options['number_of_steps'] ?? PHP_INT_MAX;

        /** @var DataTransform $transform */
        foreach ($dataFlow->getTransforms() as $index => $transform) {
            if ($index >= $numberOfSteps) {
                break;
            }

            $transformer = $this->transformerManager->getTransformer(
                $transform->getTransformer(),
                $transform->getTransformerOptions()
            );
            $columns = $transformer->transformColumns($columns);
        }

        return $columns;
    }

    public function run(DataFlow $dataFlow, array $options = []): DataFlowRunResult
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }
        $options = $this->resolveRunOptions($options);
        $result = new DataFlowRunResult($dataFlow, $options);

        $dataSet = $this->getDataSet($dataFlow);

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
                $result->addException($exception);
                // It does not make sense to continue after an exception.
                break;
            }
        }

        if ($result->isSuccess() && $numberOfSteps < \count($transforms) + 1) {
            $transform = $transforms[$numberOfSteps];
            try {
                $transformer = $this->transformerManager->getTransformer(
                    $transform->getTransformer(),
                    $transform->getTransformerOptions()
                );
                $dataSet = $transformer->transform($dataSet)->setTransform($transform);
                $result->setLookahead($dataSet);
            } catch (\Exception $exception) {
                $result->setLookaheadException($exception);
            }
        }

        // Publish result only if all transforms ran successfully.
        if ($options['publish'] && $result->isComplete()) {
            $dataSet = $result->getLastTransformResult();
            $this->publish($result, $dataSet, $dataFlow->getDataTargets());
            if ($result->isPublished()) {
                $dataFlow->setLastRunAt(new \DateTime());
                $this->entityManager->persist($dataFlow);
                $this->entityManager->flush($dataFlow);
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
