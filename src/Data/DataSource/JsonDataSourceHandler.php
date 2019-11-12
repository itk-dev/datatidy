<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Data\DataSource;

use App\Entity\JsonDataSource;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JsonDataSourceHandler implements DataSourceHandlerInterface
{
    private $client;
    private $dataSource;
    private $propertyAccessor;

    public function __construct(JsonDataSource $dataSource, HttpClientInterface $client, PropertyAccessorInterface $propertyAccessor)
    {
        $this->client = $client;
        $this->dataSource = $dataSource;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getData()
    {
        $response = $this->client->request('GET', $this->dataSource->getUrl());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $root = $this->dataSource->getRoot();
        if (!empty($root)) {
            $propertyPath = $this->buildPropertyPath($root);

            $data = $this->propertyAccessor->getValue($data, $propertyPath);
        }

        return $data;
    }

    private function buildPropertyPath(string $root): PropertyPathInterface
    {
        $propertyPathBuilder = new PropertyPathBuilder();
        $rootAsArray = explode('/', $root);

        foreach ($rootAsArray as $subPath) {
            $propertyPathBuilder->appendIndex($subPath);
        }

        return $propertyPathBuilder->getPropertyPath();
    }
}
