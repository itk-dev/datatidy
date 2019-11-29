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
use App\Util\DataTypes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;

/**
 * @DataTransformer(
 *     name="Replace value",
 *     description="Replace value"
 * )
 */
class ReplaceValueDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns", help="Choose columns")
     *
     * @var array
     */
    private $columns;

    /**
     * @Option(type="string", description="Value to search for")
     *
     * @var string
     */
    private $search;

    /**
     * @Option(type="string", description="Replacement value")
     *
     * @var string
     */
    private $replace;

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
        $columns = $this->transformColumns($input->getColumns());
        $result = $input->copy($columns->toArray())->createTable();

        foreach ($input->rows() as $row) {
            foreach ($this->columns as $column) {
                $value = $this->getValue($row, $column);
                if ($this->regexp) {
                    $value = preg_replace($this->search, $this->replace, $value);
                } else {
                    if ($this->partial) {
                        $value = str_replace($this->search, $this->replace, $value);
                    } elseif (0 === strcmp($value, $this->search)) {
                        $value = $this->replace;
                    }
                }
                $row[$column] = $value;
            }

            $result->insertRow($row);
        }

        return $result;
    }

    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $names = $columns->getKeys();
        $diff = array_diff($this->columns, $names);
        if (!empty($diff)) {
            throw new InvalidColumnException(sprintf('invalid columns: %s', implode(', ', $diff)));
        }

        $type = DataTypes::getType('string');

        return $columns->map(function (Column $column) use ($type) {
            if ($column->getType() !== $type && \in_array($column->getName(), $this->columns, true)) {
                return new Column($column->getName(), $type);
            }

            return $column;
        });
    }
}
