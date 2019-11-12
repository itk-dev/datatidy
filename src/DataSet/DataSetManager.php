<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use Doctrine\ORM\EntityManagerInterface;

class DataSetManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createDataSet(string $name, array $columns, array $items = []): DataSet
    {
        $dataSet = new DataSet($name, $this->entityManager->getConnection(), $columns);
        if (null !== $items) {
            $dataSet->createTable()->loadData($items);
        }

        return $dataSet;
    }

    public function createDataSetFromCSV(string $name, $csv, array $headers = null): DataSet
    {
        $dataSet = new DataSet($name, $this->entityManager->getConnection());
        $dataSet->buildFromCSV($csv, $headers);

        return $dataSet;
    }

    public function createDataSetFromData(string $name, array $items, array $columns = null): DataSet
    {
        $dataSet = new DataSet($name, $this->entityManager->getConnection());
        $dataSet->buildFromData($items, $columns);

        return $dataSet;
    }

    public function createDataSetFromTable(string $name): DataSet
    {
        $dataSet = new DataSet($name, $this->entityManager->getConnection());
        $dataSet->buildFromTable();

        return $dataSet;
    }
}
