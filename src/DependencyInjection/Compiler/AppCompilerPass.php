<?php


namespace App\DependencyInjection\Compiler;


use App\Annotation\DataSource;
use App\Annotation\Option;
use App\DataSource\DataSourceInterface;
use App\DataSource\DataSourceManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        AnnotationRegistry::registerLoader('class_exists');
        $reader = new CachedReader(
            new AnnotationReader(),
            new ArrayCache()
        );

        $dataSources = $this->getDataSources($container);
        $dataSources = $this->loadAnnotations(
            $container,
            $reader,
            $dataSources,
            DataSource::class,
            Option::class)
        ;

        $container->getDefinition(DataSourceManager::class)
            ->setArgument('$dataSources', $dataSources);
    }

    private function getDataSources(ContainerBuilder $container): array
    {
        $services = $container->findTaggedServiceIds('datatidy.data_source');
        $dataSources = array_filter($services, static function ($class) {
            return is_a($class, DataSourceInterface::class, true);
        }, ARRAY_FILTER_USE_KEY);

        return $dataSources;
    }

    private function loadAnnotations(ContainerBuilder $container, Reader $reader, array $services, string $annotationClass, string $optionClass): array
    {
        foreach ($services as $class => &$metaData) {

            // Make the service public so it can be loaded dynamically
            $container->getDefinition($class)->setPublic(true);

            $reflectionClass = new \ReflectionClass($class);

            $annotation = $reader->getClassAnnotation($reflectionClass, $annotationClass);
            if (!is_null($annotation)) {
                $annotation->class = $class;
                $properties = $reflectionClass->getProperties();

                foreach ($properties as $property) {
                    $option = $reader->getPropertyAnnotation($property, $optionClass);
                    if (!is_null($option)) {
                        $annotation->options[$property->getName()] = $option;
                    }
                }

                $metaData = $annotation->toArray();
            }
        }

        unset($metaData);

        return $services;
    }
}
