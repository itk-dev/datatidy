<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use App\DataTransformer\Exception\InvalidColumnException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @DataTransformer(
 *     name="Select columns",
 *     description="Select or remove columns",
 * )
 */
class SelectColumnsDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns", help="Choose columns")
     *
     * @var array
     */
    private $columns;

    /**
     * @Option(type="bool", help="If set, columns will be included. Otherwise they will be excluded.", required=false, default=true)
     *
     * @var bool
     */
    private $include = true;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform(DataSet $input): DataSet
    {
        $columns = $input->getColumns();
        $newColumns = $this->transformColumns($columns);

        $output = $input->copy($newColumns->toArray())->createTable();

        $sql = sprintf(
            'INSERT INTO %s SELECT %s FROM %s;',
            $output->getQuotedTableName(),
            implode(',', $newColumns->map(static function (Column $column) use ($input) {
                return $input->getQuotedColumnName($column->getName());
            })->getValues()),
            $input->getQuotedTableName()
        );

        return $output->buildFromSQL($sql);
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $names = $columns->getKeys();
        $diff = array_diff($this->columns, $names);
        if (!empty($diff)) {
            throw new InvalidColumnException(sprintf('invalid columns: %s', implode(', ', $diff)));
        }

        $namesToKeep = $this->include ? $this->columns : array_diff($names, $this->columns);

        return $columns->filter(static function ($value, $name) use ($namesToKeep) {
            return \in_array($name, $namesToKeep, true);
        });
    }
}
