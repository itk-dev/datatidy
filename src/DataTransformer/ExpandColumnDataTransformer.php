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
use App\DataSet\DataSetColumnList;
use App\Service\DataHelper;
use Doctrine\DBAL\Types\Type;

/**
 * @DataTransformer(
 *     name="Expand columns",
 *     description="Expands columns",
 * )
 */
class ExpandColumnDataTransformer extends AbstractDataTransformer
{
    public const DELIMITER = '/';

    /**
     * @Option(type="columns", help="Choose columns")
     *
     * @var array
     */
    private $columns;

    /** @var array */
    private $types;

    /** @var DataHelper */
    private $dataHelper;

    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    public function transform(DataSet $input): DataSet
    {
        $newColumns = $this->transformColumns($input);
        $output = $input->copy($newColumns)->createTable();

        $rows = $input->getRows();
        foreach ($this->columns as $column) {
            $rows = $this->dataHelper->expand($rows, $column, ['delimiter' => self::DELIMITER]);
        }
        $output->insertRows($rows);

        return $output;
    }

    public function transformColumns(DataSet $dataSet): DataSetColumnList
    {
        $columns = $dataSet->getColumns();
        $allColumnNames = [];
        $rows = $dataSet->getRows();
        foreach ($this->columns as $column) {
            $rows = $this->dataHelper->expand($rows, $column, ['delimiter' => self::DELIMITER]);
            $allColumnNames += array_flip(array_keys(reset($rows)));
            if (!isset($allColumnNames[$column])) {
                unset($columns[$column]);
            }
        }

        $newColumnNames = array_values(array_diff(array_keys($allColumnNames), $columns->getDisplayNames()));
        if (!empty($newColumnNames)) {
            $types = $dataSet->guessTypes($rows);

            foreach ($newColumnNames as $name) {
                $columns[] = new DataSetColumn($name, Type::getType($types[$name]));
            }
        }

        return $columns;
    }
}
