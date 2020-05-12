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
use App\DataTransformer\AbstractDataTransformer;
use App\Service\GeoJSONHelper;
use phpDocumentor\Reflection\Type;

/**
 * @DataTransformer(
 *     name="GeoJSON: Change Coordinate reference system",
 *     description="Changes Coordinate reference system",
 * )
 */
class ChangeCoordinateReferenceSystemDataTransformer extends AbstractDataTransformer
{
    /**
     * @Option(type="columns")
     *
     * @var array
     */
    private $columns;

    /**
     * @Option(type="choice", description="Source coordinate reference system", choices={
     *   "EPSG:102757",
     *   "EPSG:102758",
     *   "EPSG:2154",
     *   "EPSG:21781",
     *   "EPSG:25832",
     *   "EPSG:25833",
     *   "EPSG:26591",
     *   "EPSG:26912",
     *   "EPSG:27200",
     *   "EPSG:27561",
     *   "EPSG:27562",
     *   "EPSG:27563",
     *   "EPSG:27571",
     *   "EPSG:27572",
     *   "EPSG:28191",
     *   "EPSG:28992",
     *   "EPSG:3112",
     *   "EPSG:31258",
     *   "EPSG:31370",
     *   "EPSG:31467",
     *   "EPSG:31468",
     *   "EPSG:32636",
     *   "EPSG:32635",
     *   "EPSG:32637",
     *   "EPSG:32638",
     *   "EPSG:32639",
     *   "EPSG:32640",
     *   "EPSG:32641",
     *   "EPSG:3825",
     *   "EPSG:3826",
     *   "EPSG:3827",
     *   "EPSG:3828",
     *   "EPSG:3857",
     *   "EPSG:41001",
     *   "EPSG:4139",
     *   "EPSG:4181",
     *   "EPSG:42304",
     *   "EPSG:4269",
     *   "EPSG:4272",
     *   "EPSG:4302",
     *   "EPSG:4326",
     *   "EPSG:5514",
     *   "EPSG:900913"
     * })
     *
     * @var string
     */
    private $source;

    /**
     * @Option(type="choice", description="Target coordinate reference system", default="EPSG:4326", choices={
     *   "EPSG:102757",
     *   "EPSG:102758",
     *   "EPSG:2154",
     *   "EPSG:21781",
     *   "EPSG:25832",
     *   "EPSG:25833",
     *   "EPSG:26591",
     *   "EPSG:26912",
     *   "EPSG:27200",
     *   "EPSG:27561",
     *   "EPSG:27562",
     *   "EPSG:27563",
     *   "EPSG:27571",
     *   "EPSG:27572",
     *   "EPSG:28191",
     *   "EPSG:28992",
     *   "EPSG:3112",
     *   "EPSG:31258",
     *   "EPSG:31370",
     *   "EPSG:31467",
     *   "EPSG:31468",
     *   "EPSG:32636",
     *   "EPSG:32635",
     *   "EPSG:32637",
     *   "EPSG:32638",
     *   "EPSG:32639",
     *   "EPSG:32640",
     *   "EPSG:32641",
     *   "EPSG:3825",
     *   "EPSG:3826",
     *   "EPSG:3827",
     *   "EPSG:3828",
     *   "EPSG:3857",
     *   "EPSG:41001",
     *   "EPSG:4139",
     *   "EPSG:4181",
     *   "EPSG:42304",
     *   "EPSG:4269",
     *   "EPSG:4272",
     *   "EPSG:4302",
     *   "EPSG:4326",
     *   "EPSG:5514",
     *   "EPSG:900913"
     * })
     *
     * @var string
     */
    private $target;

    /** @var GeoJSONHelper */
    private $geoJSONHelper;

    public function __construct(GeoJSONHelper $geoJSONHelper)
    {
        $this->geoJSONHelper = $geoJSONHelper;
    }

    public function transform(DataSet $input): DataSet
    {
        $output = $input->copy()->createTable();

        foreach ($input->rows() as $row) {
            foreach ($this->columns as $column) {
                $value = $this->getValue($row, $column);
                if (\is_array($value)) {
                    $row[$column] = $this->geoJSONHelper->changeCRS($value, $this->source, $this->target);
                }
            }

            $output->insertRow($row);
        }

        return $output;
    }
}
