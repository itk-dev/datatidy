<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\DataTransformer\AbstractDataTransformer;
use App\DataTransformer\Exception\InvalidTransformerException;
use App\Traits\LogTrait;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataTargetManager
{
    use LogTrait;
    use ContainerAwareTrait;

    private $dataTargets;

    public function __construct(ContainerInterface $container, array $dataTargets)
    {
        $this->setContainer($container);
        $this->dataTargets = $dataTargets;
    }

    public function getDataTargets()
    {
        return $this->dataTargets;
    }

    /**
     * @throws InvalidTransformerException
     */
    public function getDataTarget(string $name, array $options = null): AbstractDataTarget
    {
        $transformers = $this->getDataTargets();
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

    public function getDataTargetOptions(string $name = null): array
    {
        if (null === $name) {
            return [];
        }

        return $this->getDataTarget($name)->getMetadata()['options'] ?? [];
    }
}
