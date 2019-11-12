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

/**
 * @DataTransformer(
 *     name="Rename columns",
 *     description="Renames columns"
 * )
 */
class RenameColumnsDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="map", formType="App\Form\Type\Option\ColumnStringMapType")
     *
     * @var array
     */
    private $renames;

    public function transform(DataSet $input): DataSet
    {
        $columns = $this->transformColumns($input->getColumns());
        $output = $input->copy($columns->toArray())
            ->createTable();

        $sql = sprintf(
            'INSERT INTO %s(%s) SELECT %s FROM %s;',
            $output->getQuotedTableName(),
            implode(',', $output->getQuotedColumnNames()),
            implode(',', $input->getQuotedColumnNames()),
            $input->getQuotedTableName()
        );

        return $output->buildFromSQL($sql);
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $map = [];
        foreach ($this->renames as $item) {
            $map[$item['from']] = $item['to'];
        }
        foreach ($map as $from => $to) {
            if (!$columns->containsKey($from)) {
                throw new InvalidColumnException(sprintf('Column "%s" does not exist', $from));
            }
            if ($columns->containsKey($to)) {
                throw new InvalidColumnException(sprintf('Column "%s" already exists', $to));
            }
        }

        $newColumns = new ArrayCollection();
        foreach ($columns as $name => $column) {
            if (isset($map[$name])) {
                $name = $map[$name];
                $column = $this->renameColumn($column, $name);
            }
            $newColumns[$name] = $column;
        }

        return $newColumns;
    }
}
