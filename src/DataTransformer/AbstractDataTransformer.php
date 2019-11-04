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
use App\DataTransformer\Exception\InvalidTypeException;
use App\Traits\OptionsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;

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
    abstract public function transformColumns(ArrayCollection $columns): ArrayCollection;

    public static $types = [
        'bool' => BooleanType::class,
        'int' => IntegerType::class,
        'float' => FloatType::class,
        'string' => StringType::class,
        'date' => DateType::class,
        'datetime' => DateTimeType::class,
    ];

    protected function getType(string $name): Type
    {
        if (!\array_key_exists($name, static::$types)) {
            throw new InvalidTypeException($name);
        }
        if ('int' === $name) {
            $name = 'integer';
        }

        return Type::getType($name);
    }
}
