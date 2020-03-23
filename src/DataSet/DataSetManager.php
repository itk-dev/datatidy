<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use Doctrine\DBAL\Connection;

class DataSetManager
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $dataTransformsConnection)
    {
        $this->connection = $dataTransformsConnection;
    }

    public function createDataSet(string $name, array $columns, array $items = []): DataSet
    {
        $dataSet = new DataSet($name, $this->connection, $columns);
        if (null !== $items) {
            $dataSet->createTable()->loadData($items);
        }

        return $dataSet;
    }

    public function createDataSetFromData(string $name, array $items, array $columns = null): DataSet
    {
        $dataSet = new DataSet($name, $this->connection);
        $dataSet->buildFromData($items, $columns);

        return $dataSet;
    }
}
