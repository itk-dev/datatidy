<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\DataSet\DataSet;
use App\DataTransformer\Exception\InvalidKeyException;
use App\Traits\OptionsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;

abstract class AbstractDataTransformer
{
    use OptionsTrait;

    /**
     * Transform a data set to a new data set.
     */
    abstract public function transform(DataSet $input): DataSet;

    /**
     * Transform columns.
     */
    public function transformColumns(DataSet $dataSet): ArrayCollection
    {
        return $dataSet->getColumns();
    }

    /**
     * Rename a column.
     */
    protected function renameColumn(Column $column, string $name): Column
    {
        $options = $column->toArray();
        unset($options['name']);

        return new Column($name, $column->getType(), $options);
    }

    /**
     * Note: PropertyAccessor should/could be used, but apparently it does not really check existence of array values.
     *
     * @param $propertyPath
     *
     * @return array|mixed
     */
    protected function getValue(array $value, $propertyPath)
    {
        $keys = explode('.', $propertyPath);
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $value)) {
                throw new InvalidKeyException(sprintf('Invalid key: %s', $key));
            }
            $value = $value[$key];
        }

        return $value;
    }
}
