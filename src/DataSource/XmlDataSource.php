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
use App\Annotation\DataSource\Option;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @DataSource(name="XML", description="Pulls from a XML data source")
 */
class XmlDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    /**
     * @Option(name="root", description="Root node XPath, e.g. “/data/results” – beware of namespaces!", type="string", required=false)
     */
    private $root;

    public function __construct(HttpClientInterface $httpClient, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($httpClient);
    }

    public function pull()
    {
        $response = $this->getResponse();

        throw new \RuntimeException('Lazy programmer exception: '.__METHOD__.' not implemented!');
    }
}
