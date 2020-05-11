<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer\GeoJSON;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use App\DataSet\DataSetColumnList;
use App\DataTransformer\AbstractDataTransformer;

/**
 * @DataTransformer(
 *     name="GeoJSON: Compute centriod",
 *     description="Computes the centriod of a GeoJSON object",
 * )
 */
class ComputeCentroidDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns")
     *
     * @var array
     */
    private $columns;

    public function transform(DataSet $input): DataSet
    {
        $columns = $input->getColumns();
        /** @var DataSetColumnList $centroidColumns */
        /** @var DataSetColumnList $otherColumns */
        [$centroidColumns, $otherColumns] = $input->getColumns()->partition(function (string $column) {
            return \in_array($column, $this->columns, true);
        });
        $output = $input->copy()->createTable();

        $outputNames = array_merge($centroidColumns->getSqlNames(), $otherColumns->getSqlNames());
        $inputExpressions = array_merge(array_map(static function (string $column) {
            // Leverage built-in functions in MariaDB; see https://mariadb.com/kb/en/geojson/
            return sprintf('IF(ST_GeometryType(ST_GeomFromGeoJSON(%1$s)) = \'POINT\', %1$s, ST_AsGeoJSON(ST_Centroid(ST_GeomFromGeoJSON(%1$s))))', $column);
        }, $output->getQuotedColumnNames($centroidColumns->getSqlNames())), $output->getQuotedColumnNames($otherColumns->getSqlNames()));

        $sql = sprintf(
            'INSERT INTO %s(%s) SELECT %s FROM %s;',
            $output->getQuotedTableName(),
            implode(', ', $output->getQuotedColumnNames($outputNames)),
            implode(', ', $inputExpressions),
            $input->getQuotedTableName()
        );

        return $output->buildFromSQL($sql);
    }
}
