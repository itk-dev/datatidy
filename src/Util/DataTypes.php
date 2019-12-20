<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Util;

use App\DataTransformer\Exception\InvalidTypeException;
use Doctrine\DBAL\Types\Type;

class DataTypes
{
    public static $types = [
        'boolean' => BooleanType::class,
        'int' => IntegerType::class,
        'float' => FloatType::class,
        'string' => StringType::class,
        'date' => DateType::class,
        'datetime' => DateTimeType::class,
    ];

    public static function getTypeNames()
    {
        return array_keys(static::$types);
    }

    /**
     * Get a type by name.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getType(string $name): Type
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
