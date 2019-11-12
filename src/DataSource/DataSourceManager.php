<?php


namespace App\DataSource;


use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataSourceManager
{
    use ContainerAwareTrait;

    private $container;
    private $dataSources;

    public function __construct(ContainerInterface $container, array $dataSources)
    {
        $this->container = $container;
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

    }
}
