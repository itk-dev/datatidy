<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTransformer;

use App\DataTransformer\Exception\InvalidTransformerException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataTransformerManager
{
    use ContainerAwareTrait;

    private $transformers;

    public function __construct(ContainerInterface $container, array $transformers)
    {
        $this->setContainer($container);
        $this->transformers = $transformers;
    }

    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @throws InvalidTransformerException
     */
    public function getTransformer(string $name, array $options = null): AbstractDataTransformer
    {
        $transformers = $this->getTransformers();
        if (!\array_key_exists($name, $transformers)) {
            throw new InvalidTransformerException(sprintf('Transformer with name "%s" does not exist', $name));
        }

        /** @var AbstractDataTransformer $transformer */
        $transformer = $this->container->get($name)
            ->setMetadata($transformers[$name]);
        if (null !== $options) {
            $transformer->setOptions($options);
        }

        return $transformer;
    }

    public function getTransformerOptions(string $name): array
    {
        return $this->getTransformer($name)->getMetadata()['options'] ?? [];
    }
}
