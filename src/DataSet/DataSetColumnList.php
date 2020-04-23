<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use RuntimeException;

/**
 * A list of columns indexed by data name, i.e. humanreadable names.
 */
class DataSetColumnList extends ArrayCollection
{
    private static $systemNames = [];
    private static $displayNames = [];

    public static function isSystemName(string $name): bool
    {
        return isset(self::$displayNames[$name]);
    }

    public static function isDisplayName(string $name): bool
    {
        return isset(self::$systemNames[$name]);
    }

    public static function getSqlName(string $name): string
    {
        if (!isset(static::$systemNames[$name])) {
            $systemName = '__col_'.md5($name);
            static::$systemNames[$name] = $systemName;
            static::$displayNames[$systemName] = $name;
        }

        return static::$systemNames[$name];
    }

    public static function getDisplayName(string $name): string
    {
        if (!isset(static::$displayNames[$name])) {
            throw new InvalidArgumentException(sprintf('Invalid system name: %s', $name));
        }

        return static::$displayNames[$name];
    }

    public static function createFromTable(Table $table)
    {
        $columns = new self();

        foreach ($table->getColumns() as $column) {
            $columns[] = DataSetColumn::create($column);
        }

        return $columns;
    }

    public function getKeys()
    {
        throw new RuntimeException(__METHOD__.' should not be called');
    }

    public function getNames()
    {
        return parent::getKeys();
    }

    public function getDisplayNames(): array
    {
        return array_flip($this->getSqlNames());
    }

    public function getSqlNames(): array
    {
        $names = [];
        foreach ($this as $column) {
            $systemName = $column->getSqlName();
            $displayName = $column->getDisplayName();
            $names[$displayName] = $systemName;
        }

        return $names;
    }

    /**
     * Map from system name to type.
     */
    public function getTypes(): array
    {
        return $this->map(static function (DataSetColumn $column) {
            return $column->getType();
        })->toArray();
    }

    public function add($element)
    {
        if (!$element instanceof DataSetColumn) {
            throw new InvalidArgumentException(sprintf('Element should be an instance of %s', DataSetColumn::class));
        }
        $this->set($element->getName(), $element);
    }

    public function set($key, $value)
    {
        if (!static::isDisplayName($key)) {
            throw new InvalidArgumentException(spring('Invalid column name: %s', $key));
        }
        \assert(
            $value instanceof DataSetColumn,
            new InvalidArgumentException(sprintf(
                'Value set in %s must be an instance of %s',
                self::class,
                DataSetColumn::class
            ))
        );
        parent::set($key, $value);
    }

    public function get($offset)
    {
        if (!static::isDisplayName($offset)) {
            throw new InvalidArgumentException(spring('Invalid column name: %s', $offset));
        }

        return parent::get($offset);
    }

    public function remove($name)
    {
        if (!static::isDisplayName($name)) {
            throw new InvalidArgumentException(sprintf('Invalid column name: %s', $name));
        }
        parent::remove($name);
    }
}
