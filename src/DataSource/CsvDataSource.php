<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Annotation\DataSource;
use App\DataSource\Exception\DataSourceRuntimeException;

/**
 * @DataSource(name="CSV", description="Pulls from a CSV data source")
 */
class CsvDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    public function pull()
    {
        try {
            $response = $this->getResponse();

            return $this->serializer->decode($response->getContent(), 'csv');
        } catch (\Exception $exception) {
            throw new DataSourceRuntimeException($exception->getMessage());
        }
    }
}
