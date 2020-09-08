<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

/**
 * A data set column with both a display name and a sql name.
 *
 * It basically acts as a shim around an ORM schema column.
 */
class DataSetColumn extends Column
{
    /** @var string */
    private $sqlName;

    public function __construct($columnName, Type $type, array $options = [])
    {
        parent::__construct($columnName, $type, $options);
        $this->getSqlName();
    }

    public static function create(Column $column): self
    {
        $options = $column->toArray();
        unset($options['name'], $options['type']);

        return new self($column->_name, $column->_type, $options);
    }

    /**
     * Get a name that's safe for use in sql statements.
     */
    public function getSqlName(): string
    {
        if (null === $this->sqlName) {
            $name = parent::getName();
            $this->sqlName = DataSetColumnList::isSystemName($name) ? $name : DataSetColumnList::getSqlName($name);
        }

        return $this->sqlName;
    }

    public function getDisplayName(): string
    {
        if (null === $this->sqlName) {
            throw new InvalidArgumentException(sprintf('Sql name not set (name: %s)', parent::getName()));
        }

        return DataSetColumnList::getDisplayName($this->sqlName);
    }

    public function getName()
    {
        return $this->getDisplayName();
    }
}
