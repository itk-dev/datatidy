<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Data\DataSource;

use App\Entity\CsvDataSource;
use App\Service\CsvReaderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CsvDataSourceHandler implements DataSourceHandlerInterface
{
    private $client;
    private $csvReader;
    private $dataSource;

    public function __construct(HttpClientInterface $client, CsvDataSource $dataSource, CsvReaderInterface $csvReader)
    {
        $this->client = $client;
        $this->csvReader = $csvReader;
        $this->dataSource = $dataSource;
    }

    public function getData()
    {
        $response = $this->client->request('GET', $this->dataSource->getUrl());

        $data = $this->csvReader->read($response->getContent());

        return $data;
    }
}
