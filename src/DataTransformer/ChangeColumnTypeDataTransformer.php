<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use App\DataSet\DataSetColumn;
use App\DataTransformer\Exception\InvalidColumnException;
use App\Util\DataTypes;

/**
 * @DataTransformer(
 *     name="Change type",
 *     description="Change type of columns",
 * )
 */
class ChangeColumnTypeDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns")
     *
     * @var array
     */
    private $columns;

    /**
     * @Option(type="type")
     *
     * @var string
     */
    private $type;

    public function transform(DataSet $input): DataSet
    {
        $columns = $input->getColumns();
        // @TODO Check that new type is different from current type.
        // @TODO Check that type change makes sense without data loss.
        $newColumns = clone $columns;

        $type = DataTypes::getType($this->type);
        foreach ($this->columns as $column) {
            if (!isset($newColumns[$column])) {
                throw new InvalidColumnException($column);
            }
            $newColumns[$column] = new DataSetColumn($column, $type);
        }

        $output = $input->copy($newColumns)->createTable();

        $sql = sprintf(
            'INSERT INTO %s(%s) SELECT %s FROM %s;',
            $output->getQuotedTableName(),
            implode(', ', $output->getQuotedColumnNames()),
            implode(', ', $input->getQuotedColumnNames()),
            $input->getQuotedTableName()
        );

        return $output->buildFromSQL($sql);
    }
}
