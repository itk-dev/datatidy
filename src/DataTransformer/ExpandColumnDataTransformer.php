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
        $output = $input->copy($newColumns->toArray())->createTable();

        $rows = $input->getRows();
        foreach ($this->columns as $column) {
            $rows = $this->dataHelper->expand($rows, $column, ['delimiter' => self::DELIMITER]);
        }
        $output->insertRows($rows);

        return $output;
    }

    private const TYPE_ARRAY = 'array';
    private const TYPE_OBJECT = 'object';

    public function transformColumns(DataSet $dataSet): ArrayCollection
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

        $newColumnNames = array_values(array_diff(array_keys($allColumnNames), $columns->getKeys()));
        if (!empty($newColumnNames)) {
            $types = $dataSet->guessTypes($rows);

            foreach ($newColumnNames as $name) {
                $columns[$name] = new Column($name, Type::getType($types[$name]));
            }
        }

        return $columns;
    }
}
