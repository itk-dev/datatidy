<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Annotation\Option;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractDataSource implements DataSourceInterface
{
    protected $httpClient;

    /**
     * @Option(name="URL", description="Endpoint URL", type="string")
     */
    protected $url;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    abstract public function pull();
}
