<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DependencyInjection\Compiler;

use App\Annotation\DataTransformer;
use App\Calculator\Manager;
use App\DataTransformer\AbstractDataTransformer;
use App\DataTransformer\DataTransformerManager;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $annotationReader = $container->get('annotation_reader');

        $services = $container->findTaggedServiceIds('datatidy.data_Transformer');
        $transformers = array_filter($services, static function ($class) {
            return is_a($class, AbstractDataTransformer::class, true);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($transformers as $class => &$metadata) {
            // Make the transformer service public so the manager can load it dynamically.
            $container->getDefinition($class)->setPublic(true);
            $reflectionClass = new ReflectionClass($class);
            /** @var DataTransformer $annotation */
            $annotation = $annotationReader->getClassAnnotation($reflectionClass, DataTransformer::class);
            if (null !== $annotation) {
                $annotation->class = $class;
                $properties = $reflectionClass->getProperties();
                foreach ($properties as $property) {
                    $option = $annotationReader->getPropertyAnnotation($property, DataTransformer\Option::class);
                    if (null !== $option) {
                        $annotation->options[$property->getName()] = $option;
                    }
                }
                $metadata = $annotation->toArray();
            }
        }
        unset($metadata);

        $definition = $container->getDefinition(DataTransformerManager::class);
        $definition->setArgument('$transformers', $transformers);
    }
}
