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

    /**
     * @return ArrayCollection
     */
    public function runColumns(DataFlow $dataFlow, array $options = [])
    {
        $dataSet = $this->getDataSet($dataFlow);

        $columns = $dataSet->getColumns();
        $steps = $options['steps'] ?? PHP_INT_MAX;

        /** @var DataTransform $transform */
        foreach ($dataFlow->getTransforms() as $index => $transform) {
            if ($index >= $steps) {
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
        $run = new DataFlowRunResult($dataFlow, $options);

        $dataSet = $this->getDataSet($dataFlow);

        $run->addDataSet($dataSet);
        $steps = $options['steps'] ?? PHP_INT_MAX;
        /** @var DataTransform $transform */
        foreach ($dataFlow->getTransforms() as $index => $transform) {
            if ($index >= $steps) {
                break;
            }
            try {
                $transformer = $this->transformerManager->getTransformer(
                    $transform->getTransformer(),
                    $transform->getTransformerOptions()
                );
                $dataSet = $transformer->transform($dataSet)->setTransform($transform);
                $run->addDataSet($dataSet);
            } catch (\Exception $exception) {
                $run->addException($exception);
                // It does not make sense to continue after an exception.
                break;
            }
        }

        // Publish result only if all transforms ran successfully.
        if (($options['publish'] ?? false) && $run->isComplete()) {
            $result = $run->getLastDataSet();
            $this->publish($result, $dataFlow->getDataTargets());
        }

        return $run;
    }

    private function publish(DataSet $result, Collection $dataTargets)
    {
        $rows = $result->getRows();
        $this->dataTargetManager->setLogger($this->logger);
        foreach ($dataTargets as $dataTarget) {
            $this->debug(sprintf('publish: %s', $dataTarget));
            $target = $this->dataTargetManager->getDataTarget($dataTarget->getDataTarget(), $dataTarget->getDataTargetOptions());
            $target->setLogger($this->logger);
            $data = $dataTarget->getData() ?? [];
            $target->publish($rows, $result->getColumns(), $data);
            $dataTarget->setData($data);
            $this->entityManager->persist($dataTarget);
            $this->entityManager->flush();
        }
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
}
