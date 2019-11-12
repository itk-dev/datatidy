<?php

namespace App\DataSource;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataSourceManager
{
    use ContainerAwareTrait;

    private $dataSources;

    public function __construct(array $dataSources)
    {
        $this->dataSources = $dataSources;
    }

    public function getDataSources(): array
    {
        return $this->dataSources;
    }

    public function getDataSource(string $name, array $options = null)
    {
        $dataSources = $this->getDataSources();
        if (!\array_key_exists($name, $dataSources)) {
            throw new \Exception('DataSource not found!');
        }

        /** @var AbstractDataSource $dataSource */
        $dataSource = $this->container->get($name);
    }

    public function getDataSourceOptions($dataSource): array
    {
        return [];
    }

    public function getData(AbstractDataSource $dataSource)
    {
        $data = [
            [
                'id' => 87,
                'name' => 'Mikkel',
                'birthday' => '1975-05-23',
            ],
            [
                'id' => 42,
                'name' => 'James',
                'birthday' => '1963-08-03',
            ],
        ];

        return $data;
    }
}
