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
use Symfony\Component\PropertyAccess\PropertyPathBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @DataSource(name="JSON", description="Pulls from a JSON data source")
 */
class JsonDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    /**
     * @Option(name="root", description="Root node, e.g. “data.results”", type="string")
     */
    private $root;

    private $propertyAccessor;

    public function __construct(HttpClientInterface $httpClient, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($httpClient);
        $this->propertyAccessor = $propertyAccessor;
    }

    public function pull()
    {
        $response = $this->getResponse();

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!empty($this->root)) {
            $propertyPath = $this->buildPropertyPath($this->root);

            $data = $this->propertyAccessor->getValue($data, $propertyPath);
        }

        return $data;
    }

    private function buildPropertyPath(string $root): PropertyPathInterface
    {
        $propertyPathBuilder = new PropertyPathBuilder();
        $paths = explode('.', $root);

        foreach ($paths as $path) {
            $propertyPathBuilder->appendIndex($path);
        }

        return $propertyPathBuilder->getPropertyPath();
    }
}
