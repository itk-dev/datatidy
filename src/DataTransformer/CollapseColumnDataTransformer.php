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
use App\Service\DataHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

/**
 * @DataTransformer(
 *     name="Collapse columns",
 *     description="Collapse columns",
 * )
 */
class CollapseColumnDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="collapse_columns", help="Choose columns")
     *
     * @var array
     */
    private $columns;

    /** @var DataHelper */
    private $dataHelper;

    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    public function transform(DataSet $input): DataSet
    {
        $newColumns = $this->transformColumns($input);
        $output = $input->copy($newColumns->toArray())->createTable();

        $rows = $input->getRows();
        foreach ($this->columns as $column) {
            $rows = $this->dataHelper->collapse($rows, $column, ['delimiter' => ExpandColumnDataTransformer::DELIMITER]);
        }
        $output->insertRows($rows);

        return $output;
    }

    public function transformColumns(DataSet $dataSet): ArrayCollection
    {
        $columns = $dataSet->getColumns();

        $transformedColumnNames = [];
        $rows = $dataSet->getRows();
        foreach ($this->columns as $column) {
            $rows = $this->dataHelper->collapse($rows, $column, ['delimiter' => ExpandColumnDataTransformer::DELIMITER]);
            $transformedColumnNames = array_keys(reset($rows));
        }

        $columnsToRemove = array_diff($columns->getKeys(), $transformedColumnNames);
        foreach ($columnsToRemove as $column) {
            $columns->remove($column);
        }

        $newColumnNames = array_values(array_diff($transformedColumnNames, $columns->getKeys()));
        if (!empty($newColumnNames)) {
            $types = $dataSet->guessTypes($rows);

            foreach ($newColumnNames as $name) {
                $columns[$name] = new Column($name, Type::getType($types[$name]));
            }
        }

        return $columns;
    }
}
