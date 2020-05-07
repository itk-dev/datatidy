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

    public function createDataSetFromData(string $name, array $items, array $columns = null): DataSet
    {
        $dataSet = new DataSet($name, $this->entityManager->getConnection());
        $dataSet->buildFromData($items, $columns);

        return $dataSet;
    }
}
