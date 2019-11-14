<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Entity\DataSource;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DataSourceManager
{
    use ContainerAwareTrait;

    private $dataSources;

    public function __construct(ContainerInterface $container, array $dataSources)
    {
        $this->setContainer($container);
        $this->dataSources = $dataSources;
    }

    public function getDataSources(): array
    {
        return $this->dataSources;
    }

    /**
     * @return AbstractDataSource
     *
     * @throws \Exception
     */
    public function getDataSource(string $name, array $options = null)
    {
        $dataSources = $this->getDataSources();
        if (!\array_key_exists($name, $dataSources)) {
            throw new \Exception('DataSource not found!');
        }

        /** @var AbstractDataSource $dataSource */
        $dataSource = $this->container->get($name)
            ->setMetadata($dataSources[$name]);
        if (null !== $options) {
            $dataSource->setOptions($options);
        }

        return $dataSource;
    }

    public function getDataSourceOptions($name): array
    {
        if (null === $name) {
            return [];
        }

        return $this->getDataSource($name)->getMetadata()['options'] ?? [];
    }

    public function getData(DataSource $dataSource)
    {
        $source = $this->getDataSource($dataSource->getDataSource(), $dataSource->getDataSourceOptions());

        return $source->pull();
    }
}
