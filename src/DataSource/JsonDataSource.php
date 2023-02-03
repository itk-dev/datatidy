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
use App\Annotation\DataSource\Option;
use App\DataSource\Exception\DataSourceRuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @DataSource(name="JSON", description="Pulls from a JSON data source")
 */
class JsonDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    /**
     * @Option(name="root", description="Root node, e.g. “data.results”", type="string", required=false)
     */
    private $root;

    private $propertyAccessor;

    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($httpClient, $serializer);
        $this->propertyAccessor = $propertyAccessor;
    }

    public function pull()
    {
        try {
            $response = $this->getResponse();

            $data = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if (!empty($this->root)) {
                $propertyPath = $this->buildPropertyPath($this->root);

                $data = $this->propertyAccessor->getValue($data, $propertyPath);
            }

            // We must return an array.
            if (!$this->isArray($data)) {
                $data = [$data];
            }

            return $data;
        } catch (\Exception $exception) {
            throw new DataSourceRuntimeException($exception->getMessage());
        }
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
