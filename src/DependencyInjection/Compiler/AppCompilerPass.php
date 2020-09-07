<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DependencyInjection\Compiler;

use App\Annotation\AbstractAnnotation;
use App\Annotation\DataSource;
use App\Annotation\DataTarget;
use App\Annotation\DataTransformer;
use App\DataSource\AbstractDataSource;
use App\DataSource\DataSourceManager;
use App\DataTarget\AbstractDataTarget;
use App\DataTarget\DataTargetManager;
use App\DataTransformer\AbstractDataTransformer;
use App\DataTransformer\DataTransformerManager;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppCompilerPass implements CompilerPassInterface
{
    /** @var ContainerBuilder */
    private $container;

    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->collectServices(
            'datatidy.data_transformer',
            AbstractDataTransformer::class,
            DataTransformer::class,
            DataTransformerManager::class,
            '$transformers'
        );

        $this->collectServices(
            'datatidy.data_target',
            AbstractDataTarget::class,
            DataTarget::class,
            DataTargetManager::class,
            '$dataTargets'
        );

        $this->collectServices(
            'datatidy.data_source',
            AbstractDataSource::class,
            DataSource::class,
            DataSourceManager::class,
            '$dataSources'
        );
    }

    /**
     * Collection tagged services, extract metadata and options, and inject into service manager.
     */
    private function collectServices(
        string $tag,
        string $serviceClass,
        string $serviceAnnotationClass,
        string $managerClass,
        string $managerArgumentName
    ) {
        if (!is_a($serviceAnnotationClass, AbstractAnnotation::class, true)) {
            throw new \RuntimeException(sprintf('%s must be an instance of %s', $serviceAnnotationClass, AbstractAnnotation::class));
        }
        // Get value of static property AbstractAnnotation::$optionClass.
        $property = new ReflectionProperty($serviceAnnotationClass, 'optionClass');
        $property->setAccessible(true);
        $optionAnnotationClass = $property->getValue();
        if (!is_a($optionAnnotationClass, AbstractAnnotation\AbstractOption::class, true)) {
            throw new \RuntimeException(sprintf('%s must be an instance of %s', $optionAnnotationClass, AbstractAnnotation\AbstractOption::class));
        }

        $services = $this->container->findTaggedServiceIds($tag);
        $services = array_filter($services, static function ($class) use ($serviceClass) {
            return is_a($class, $serviceClass, true);
        }, ARRAY_FILTER_USE_KEY);

        $annotationReader = $this->container->get('annotation_reader');

        foreach ($services as $class => &$metadata) {
            // Make the service public so the manager can load it dynamically.
            $this->container->getDefinition($class)->setPublic(true);
            $reflectionClass = new ReflectionClass($class);
            /** @var AbstractAnnotation $annotation */
            $annotation = $annotationReader->getClassAnnotation($reflectionClass, $serviceAnnotationClass);
            if (null !== $annotation) {
                $annotation->class = $class;
                $properties = $reflectionClass->getProperties();
                foreach ($properties as $property) {
                    $option = $annotationReader->getPropertyAnnotation($property, $optionAnnotationClass);
                    if (null !== $option) {
                        $annotation->options[$property->getName()] = $option;
                    }
                }
                uasort($annotation->options, function (AbstractAnnotation\AbstractOption $a, AbstractAnnotation\AbstractOption $b) {
                    return $a->order <=> $b->order;
                });

                $metadata = $annotation->toArray();
            }
        }
        unset($metadata);

        $definition = $this->container->getDefinition($managerClass);
        $definition->setArgument($managerArgumentName, $services);

        return $services;
    }
}
