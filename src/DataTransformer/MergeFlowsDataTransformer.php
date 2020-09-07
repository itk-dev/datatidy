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
use App\DataFlow\DataFlowManager;
use App\DataSet\DataSet;
use App\DataSet\DataSetColumnList;
use App\DataTransformer\Exception\TransformerRuntimeException;
use App\Entity\DataFlow;

/**
 * @DataTransformer(
 *     name="Merge flows",
 *     description="Merge a data flow into this flow"
 * )
 */
class MergeFlowsDataTransformer extends AbstractDataTransformer
{
    public const JOIN_TYPE_INNER = 'inner';
    public const JOIN_TYPE_LEFT = 'left';
    public const JOIN_TYPE_RIGHT = 'right';
    public const JOIN_TYPE_CROSS = 'cross';

    /**
     * @Option(type="data_flow")
     *
     * @var DataFlow
     */
    private $dataFlow;

    /**
     * @Option(
     *     type="choice",
     *     name="Join type",
     *     choices={MergeFlowsDataTransformer::JOIN_TYPE_INNER, MergeFlowsDataTransformer::JOIN_TYPE_LEFT, MergeFlowsDataTransformer::JOIN_TYPE_RIGHT, MergeFlowsDataTransformer::JOIN_TYPE_CROSS},
     *     default="MergeFlowsDataTransformer::JOIN_TYPE_INNER"
     * )
     *
     * @var string
     */
    private $joinType;

    /**
     * @Option(type="columns", name="Join columns", description="Columns to join on. If none are selected, join on all shared columns. Ignored for cross join.")
     *
     * @var string[]
     */
    private $joinColumns;

    /**
     * @Option(
     *     type="bool",
     *     name="Include all columns",
     *     description="If set, all columns from joined data flow are added. Otherwise only columns that do not already exist are added.",
     *     required=false,
     *     default=false
     * )
     *
     * @var bool
     */
    private $includeAllColumns;

    /** @var DataFlowManager */
    private $dataFlowManager;

    public function __construct(DataFlowManager $dataFlowManager)
    {
        $this->dataFlowManager = $dataFlowManager;
    }

    public function transform(DataSet $input): DataSet
    {
        $dataSet = $this->getDataSet();
        $leftColumns = $input->getColumns();
        $leftSystemNames = $leftColumns->getSqlNames();
        $rightColumns = $this->getRightColumns();
        $rightSystemNames = $rightColumns->getSqlNames();
        $joinColumns = array_map(
            static function ($name) use ($input, $leftSystemNames, $rightSystemNames) {
                return $input->getQuotedColumnName($leftSystemNames[$name] ?? $rightSystemNames[$name], false);
            },
            $this->getJoinColumns($leftColumns, $rightColumns)
        );

        $newColumns = $this->transformColumns($input);

        $output = $input->copy($newColumns)->createTable();

        $leftTableName = $input->getQuotedTableName();
        $rightTableName = $dataSet->getQuotedTableName();

        $joinType = function () {
            switch ($this->joinType) {
                case static::JOIN_TYPE_LEFT:
                    return 'LEFT JOIN';
                case static::JOIN_TYPE_RIGHT:
                    return 'RIGHT JOIN';
                case static::JOIN_TYPE_CROSS:
                    return 'CROSS JOIN';
                case static::JOIN_TYPE_INNER:
                default:
                    return 'INNER JOIN';
            }
        };

        $joinExpression = static function () use ($leftTableName, $rightTableName, $joinColumns) {
            if (empty($joinColumns)) {
                return '';
            }

            return 'ON '.implode(' AND ', array_map(static function ($name) use ($leftTableName, $rightTableName) {
                return sprintf('%s.%s = %s.%s', $leftTableName, $name, $rightTableName, $name);
            }, $joinColumns));
        };

        $selectColumns = [];
        if (self::JOIN_TYPE_RIGHT !== $this->joinType) {
            foreach ($leftColumns as $column) {
                $selectColumns[] = sprintf('%1$s.%2$s AS %2$s', $leftTableName, $input->getQuotedColumnName($column->getSqlName()));
            }

            foreach ($rightColumns as $name => $column) {
                if ($this->includeAllColumns) {
                    $alias = $this->getRightColumnAlias($name);
                    $selectColumns[] = sprintf(
                        '%1$s.%2$s AS %3$s',
                        $rightTableName,
                        $dataSet->getQuotedColumnName($column->getSqlName()),
                        $dataSet->getQuotedColumnName($alias)
                    );
                } elseif (!$leftColumns->containsKey($name)) {
                    $selectColumns[] = sprintf(
                        '%1$s.%2$s AS %3$s',
                        $rightTableName,
                        $dataSet->getQuotedColumnName($column->getSqlName()),
                        $dataSet->getQuotedColumnName($name)
                    );
                }
            }
        } else {
            foreach ($leftColumns as $name => $column) {
                if ($this->includeAllColumns) {
                    $alias = $this->getLeftColumnAlias($name);
                    $selectColumns[] = sprintf(
                        '%1$s.%2$s AS %3$s',
                        $leftTableName,
                        $dataSet->getQuotedColumnName($column->getSqlName()),
                        $dataSet->getQuotedColumnName($alias)
                    );
                } elseif (!$rightColumns->containsKey($name)) {
                    $selectColumns[] = sprintf(
                        '%1$s.%2$s AS %3$s',
                        $leftTableName,
                        $dataSet->getQuotedColumnName($column->getSqlName()),
                        $dataSet->getQuotedColumnName($name)
                    );
                }
            }

            foreach ($rightColumns as $name => $column) {
                $selectColumns[] = sprintf('%1$s.%2$s AS %2$s', $rightTableName, $input->getQuotedColumnName($column->getSqlName()));
            }
        }

        $sql = sprintf(
            'INSERT INTO %s SELECT %s FROM %s %s %s %s;',
            $output->getQuotedTableName(),
            implode(',', $selectColumns),
            $leftTableName,
            $joinType(),
            $rightTableName,
            $joinExpression(),
        );

        return $output->buildFromSQL($sql);
    }

    public function transformColumns(DataSet $dataSet): DataSetColumnList
    {
        $leftColumns = $dataSet->getColumns();
        $rightColumns = $this->getRightColumns();

        $columns = new DataSetColumnList();
        if (self::JOIN_TYPE_RIGHT !== $this->joinType) {
            foreach ($leftColumns as $name => $column) {
                $columns[] = $column;
            }
            foreach ($rightColumns as $name => $column) {
                if ($this->includeAllColumns) {
                    $name = $this->getRightColumnAlias($name);
                    $columns[] = $this->renameColumn($column, $name);
                } elseif (!$leftColumns->containsKey($name)) {
                    $columns[] = $column;
                }
            }
        } else {
            foreach ($leftColumns as $name => $column) {
                if ($this->includeAllColumns) {
                    $name = $this->getLeftColumnAlias($name);
                    $columns[] = $this->renameColumn($column, $name);
                } elseif (!$rightColumns->containsKey($name)) {
                    $columns[] = $column;
                }
            }
            foreach ($rightColumns as $name => $column) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function getLeftColumnAlias($name)
    {
        return '_left_'.$name;
    }

    private function getRightColumnAlias($name)
    {
        return '_right_'.$name;
    }

    private function getJoinColumns(DataSetColumnList $columns, DataSetColumnList $otherColumns)
    {
        if (self::JOIN_TYPE_CROSS === $this->joinType) {
            return [];
        }

        return !empty($this->joinColumns) ? $this->joinColumns : array_intersect($columns->getDisplayNames(), $otherColumns->getDisplayNames());
    }

    private $rightColumns;

    private function getRightColumns(): DataSetColumnList
    {
        if (null === $this->rightColumns) {
            $dataFlow = $this->getDataFlow();

            $this->rightColumns = $this->dataFlowManager->runColumns($dataFlow);
        }

        return $this->rightColumns;
    }

    private function getDataFlow(): ?DataFlow
    {
        if (null !== $this->dataFlow) {
            if (!$this->dataFlow instanceof DataFlow) {
                return $this->dataFlowManager->getDataFlow($this->dataFlow);
            }

            return $this->dataFlow;
        }

        throw new \RuntimeException('Cannot get data flow');
    }

    private function getDataSet()
    {
        $result = $this->dataFlowManager->run($this->getDataFlow());

        if ($result->hasTransformException()) {
            $exception = $result->getTransformException();
            throw new TransformerRuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $result->getLastTransformResult();
    }
}
