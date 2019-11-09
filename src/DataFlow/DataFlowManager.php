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

    public function getDataFlow(int $id): ?DataFlow
    {
        return $this->repository->find($id);
    }

    public function getDataSourceManager(): DataSourceManager
    {
        return $this->dataSourceManager;
    }

    /**
     * @return ArrayCollection[]
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

    /**
     * @return DataSet[]
     */
    public function run(DataFlow $dataFlow, array $options = [])
    {
        $dataSet = $this->getDataSet($dataFlow);

        $results = [$dataSet];
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
                $results[] = $dataSet;
            } catch (AbstractTransformerException $exception) {
                if ($options['return_exceptions'] ?? false) {
                    $results[] = (new TransformRuntimeException('Data wrangler run failed', $exception->getCode(), $exception))
                        ->setTransform($transform);
                    break;
                } else {
                    throw $exception;
                }
            }
        }

        // Publish result only if running all transforms.
        if ((!isset($options['steps']) || $dataFlow->getTransforms()->count() === $options['steps'])
            && ($options['publish'] ?? false)) {
            $result = end($results);
            $this->publish($result, $dataFlow->getDataTargets());
        }

        return $results;
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
