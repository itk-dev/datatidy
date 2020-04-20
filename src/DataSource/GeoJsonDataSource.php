<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Annotation\DataSource;

/**
 * @DataSource(name="GeoJSON", description="Pulls from a GeoJSON data source")
 */
class GeoJsonDataSource extends JsonDataSource
{
    public function pull()
    {
        // Make sure that we return an array.
        return [parent::pull()];
    }
}
