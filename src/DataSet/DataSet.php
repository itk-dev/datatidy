<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSet;

use App\Data\Exception\InvalidTableNameException;
use App\Entity\DataTransform;
use App\Service\DataHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

class DataSet
{
    /** @var Connection */
    private $connection;

    /** @var AbstractPlatform */
    private $platform;

    /** @var string */
    private $name;

    /**
     * The underlying table holding the data and schema.
     *
     * @var Table
     */
    private $table;

    /** @var DataTransform */
    private $transform;

    // @TODO Move table name prefix ("__data_set") to configuration and probably the DataSetManager.
    private $tableNamePrefix = '__data_set_';

    /**
     * Table constructor.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(string $name, Connection $connection, DataSetColumnList $columns = null)
    {
        if (empty($name)) {
            throw new \RuntimeException('Data set name cannot be empty');
        }
        $this->name = $name;
        $this->connection = $connection;
        $this->platform = $this->connection->getDatabasePlatform();

        if (null !== $columns) {
            $this->table = $this->buildTable($columns);
        }
    }

    public function getTransform(): ?DataTransform
    {
        return $this->transform;
    }

    public function setTransform(DataTransform $transform)
    {
        $this->transform = $transform;

        return $this;
    }

    // @TODO Generating names should be handled by the data set mananger.
    public function getNewName()
    {
        $name = $this->name;

        $pattern = '/_(?P<number>\d+)$/';
        $format = '%03d';
        if (preg_match($pattern, $name, $matches)) {
            return preg_replace($pattern, '_'.sprintf($format, ((int) $matches['number']) + 1), $this->name);
        }

        return $this->name.'_'.sprintf($format, 0);
    }

    public function copy(DataSetColumnList $columns = null)
    {
        $name = $this->getNewName();

        return new static($name, $this->connection, $columns ?? $this->getColumns());
    }

    public function buildFromData(array $items, array $columns = null): self
    {
        if (null === $this->table) {
            if (null === $columns) {
                $columns = $this->buildColumns($items);
            }
            $this->table = $this->buildTable($columns);
        }
        $this->createTable();

        return $this->loadData($items);
    }

    public function isDataSetTableName(string $name)
    {
        return \strlen($name) > \strlen($this->tableNamePrefix) && 0 === strpos($name, $this->tableNamePrefix);
    }

    public function getDataSetName(string $tableName)
    {
        if (!$this->isDataSetTableName($tableName)) {
            throw new InvalidTableNameException(sprintf('Invalid data set table name: %s', $tableName));
        }

        return preg_replace('@^'.preg_quote($this->tableNamePrefix, '@').'@', '', $tableName);
    }

    public function buildFromSQL(string $sql): self
    {
        $statement = $this->prepare($sql);

        $statement->execute();

        return $this;
    }

    public function getTableName()
    {
        return $this->table->getName();
    }

    public function getQuotedTableName()
    {
        return $this->quoteName($this->getTableName());
    }

    public function getQuotedColumnNames(array $names = null)
    {
        if (null === $names) {
            $names = $this->getColumns()->getSqlNames();
        }

        return array_combine($names, array_map([$this, 'quoteName'], $names));
    }

    public function getQuotedColumnName(string $name)
    {
        return $this->quoteName($name);
    }

    /**
     * Get table columns indexed by their real name (and not a normalized (e.g. down cased) name).
     */
    public function getColumns(): DataSetColumnList
    {
        return DataSetColumnList::createFromTable($this->table);
    }

    public function getRows(): array
    {
        return iterator_to_array($this->rows());
    }

    public function rows(): ?\Generator
    {
        $statement = sprintf('SELECT * FROM %s;', $this->getQuotedTableName());
        $rowsStatement = $this->prepare($statement);
        $rowsStatement->execute();
        $columns = $this->getColumns();
        $displayNames = $columns->getDisplayNames();

        // @TODO Can we improve this by aliasing column names in the select?
        // Aliases have a max length of 256 characters (and are silently truncated).
        while ($row = $rowsStatement->fetch()) {
            yield DataHelper::remap(function ($key, $value) use ($columns, $displayNames) {
                $displayName = $displayNames[$key];

                return [$displayName => $columns[$displayName]->getType()->convertToPHPValue($value, $this->platform)];
            }, $row);
        }
    }

    public function insertRows(array $rows)
    {
        $columns = $this->getColumns();
        $sqlNames = $columns->getSqlNames();

        $sql = sprintf(
            'INSERT INTO %s(%s) VALUES (%s);',
            $this->getQuotedTableName(),
            implode(',', array_map([$this, 'quoteName'], $sqlNames)),
            implode(',', array_map(static function ($name) {
                return ':'.$name;
            }, $sqlNames))
        );
        $statement = $this->prepare($sql);

        foreach ($rows as $row) {
            foreach ($row as $name => $value) {
                /** @var Type $type */
                $type = $columns[$name]->getType();

                if (\is_array($value)) {
                    $value = json_encode($value, \JSON_THROW_ON_ERROR, 512);
                }
                $statement->bindValue($sqlNames[$name], $type->convertToPHPValue($value, $this->platform), $type);
            }

            $result = $statement->execute();
            if (!$result) {
                return $result;
            }
        }

        return true;
    }

    public function insertRow(array $row)
    {
        return $this->insertRows([$row]);
    }

    /**
     * Wrapper around Connection::prepare().
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @see Connection::prepare()
     */
    private function prepare(string $statement): \Doctrine\DBAL\Driver\Statement
    {
        if (null === $this->connection) {
            throw new \RuntimeException('No connection set on data source');
        }

        return $this->connection->prepare($statement);
    }

    /**
     * @return bool|string
     */
    public function toCSV()
    {
        $buffer = fopen('php://temp', 'rb+');
        foreach ($this->rows() as $index => $item) {
            if (0 === $index) {
                fputcsv($buffer, array_keys($item));
            }
            fputcsv($buffer, array_map(static function ($value) {
                if ($value instanceof \DateTime) {
                    return $value->format(\DateTime::ATOM);
                }

                return $value;
            }, $item));
        }
        rewind($buffer);
        $csv = stream_get_contents($buffer);
        fclose($buffer);

        return $csv;
    }

    private function quoteName($name): string
    {
        return $this->platform->quoteIdentifier($name);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Build database table.
     *
     * @param string|null $tableName
     *
     * @return Table
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function buildTable(DataSetColumnList $columns)
    {
        $tableName = $this->quoteName($this->tableNamePrefix.$this->getName());
        $table = new Table($tableName);
        foreach ($columns as $column) {
            $name = $column->getSqlName();
            $type = $column->getType()->getName();
            $table->addColumn($this->quoteName($name), $type, [
                'notnull' => false,
            ]);
        }

        return $table;
    }

    /**
     * Create table in database.
     *
     * @return DataSet
     */
    public function createTable(): self
    {
        if (null === $this->connection) {
            throw new \RuntimeException('No connection set on data source');
        }

        $schema = $this->connection->getSchemaManager();
        $schema->dropAndCreateTable($this->table);

        return $this;
    }

    public function dropTable(): self
    {
        $schema = $this->connection->getSchemaManager();
        $schema->dropTable($this->table);

        return $this;
    }

    /**
     * Load data into database table.
     *
     * @return $this
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function loadData(array $items, bool $truncate = true): self
    {
        if ($truncate) {
            $this->clearTable();
        }
        $this->insertRows($items);

        return $this;
    }

    public function clearTable()
    {
        $sql = sprintf(
            'TRUNCATE %s;',
            $this->getQuotedTableName()
        );

        return $this->prepare($sql)->execute();
    }

    private function buildColumns(array $items): DataSetColumnList
    {
        $columns = new DataSetColumnList();
        if (!empty($items)) {
            $names = array_keys(reset($items));
            $types = $this->guessTypes($items);
            foreach ($names as $name) {
                $columns->add(new DataSetColumn($name, Type::getType($types[$name])));
            }
        }

        return $columns;
    }

    private static function getValue($value, Type $type)
    {
        if (filter_var($value, \FILTER_VALIDATE_INT)) {
            return (int) $value;
        }
        if (filter_var($value, \FILTER_VALIDATE_FLOAT)) {
            return (float) $value;
        }

        return $value;
    }

    /**
     * Guess type of a column.
     *
     * @return array The type
     */
    public function guessTypes(array $items)
    {
        $types = [];

        $item = reset($items);
        if (\is_array($item)) {
            foreach ($item as $name => $value) {
                $values = array_column($items, $name);
                $types[$name] = $this->guessType($values);
            }
        }

        return $types;
    }

    /**
     * Guess type of a list of values. Data from non-json sources are always strings, but we want to work with real values.
     *
     * @return int|string
     */
    private function guessType(array $values)
    {
        $votes = [
            Type::INTEGER => 0,
            Type::FLOAT => 0,
            Type::DATETIME => 0,
            Type::DATE => 0,
            Type::JSON => 0,
        ];
        $maxLength = 0;
        $numberOfValues = 0;
        foreach ($values as $value) {
            // Null values should not count.
            if (null === $value) {
                continue;
            }
            if (false !== filter_var($value, \FILTER_VALIDATE_INT)) {
                ++$votes[Type::INTEGER];
            }
            if (false !== filter_var($value, \FILTER_VALIDATE_FLOAT)) {
                ++$votes[Type::FLOAT];
            }
            if (!is_scalar($value)) {
                ++$votes[Type::JSON];
            }
            if (\is_string($value) && !empty($value)) {
                $length = \strlen($value);
                if ($length > $maxLength) {
                    $maxLength = $length;
                }
            }

            ++$numberOfValues;
        }

        if ($numberOfValues > 0) {
            foreach ($votes as $type => $count) {
                if ($numberOfValues === $count) {
                    return $type;
                }
            }
        }

        return $maxLength > 255 ? Type::TEXT : Type::STRING;
    }

    public function __toString()
    {
        return $this->name ?? static::class;
    }
}
