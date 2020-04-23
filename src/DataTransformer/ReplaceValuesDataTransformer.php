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
use App\DataSet\DataSetColumn;
use App\DataSet\DataSetColumnList;
use App\DataTransformer\Exception\InvalidColumnException;
use App\Util\DataTypes;
use Doctrine\DBAL\Schema\Column;

/**
 * @DataTransformer(
 *     name="Replace values",
 *     description="Replace values"
 * )
 */
class ReplaceValuesDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns", help="Choose columns")
     *
     * @var array
     */
    private $columns;

    /**
     * @Option(type="map", formType="App\Form\Type\Option\StringStringMapType", description="Search and replace values", default={{"from":"", "to":""}})
     *
     * @var string
     */
    private $replacements;

    /**
     * @Option(type="bool", description="If set, a partial match is done when searching. Otherwise the entire value must match. Ignored when using regular expressions.", required=false, default=false),
     *
     * @var bool
     */
    private $partial;

    /**
     * @Option(type="bool", description="If set, use regular expressions for search and replace", required=false, default=false),
     * *
     *
     * @var bool
     */
    private $regexp;

    public function transform(DataSet $input): DataSet
    {
        $columns = $this->transformColumns($input);
        $result = $input->copy($columns)->createTable();

        $search = array_column($this->replacements, 'from');
        $replace = array_column($this->replacements, 'to');

        // Use regular expression for non-partial replace on whole string.
        if (!$this->regexp && !$this->partial) {
            $search = array_map(static function ($s) {
                return '/^'.preg_quote($s, '/').'$/';
            }, $search);
        }

        foreach ($input->rows() as $row) {
            foreach ($this->columns as $column) {
                $value = $this->getValue($row, $column);
                if ($this->regexp) {
                    $value = preg_replace($search, $replace, $value);
                } else {
                    if ($this->partial) {
                        $value = str_replace($search, $replace, $value);
                    } else {
                        $value = preg_replace($search, $replace, $value);
                    }
                }
                $row[$column] = $value;
            }

            $result->insertRow($row);
        }

        return $result;
    }

    public function transformColumns(DataSet $dataSet): DataSetColumnList
    {
        $columns = $dataSet->getColumns();
        $names = $columns->getNames();
        $diff = array_diff($this->columns, $names);
        if (!empty($diff)) {
            throw new InvalidColumnException(sprintf('invalid columns: %s', implode(', ', $diff)));
        }

        $type = DataTypes::getType('string');

        return $columns->map(function (Column $column) use ($type) {
            if ($column->getType() !== $type && \in_array($column->getName(), $this->columns, true)) {
                return new DataSetColumn($column->getName(), $type);
            }

            return $column;
        });
    }
}
