<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Manager;

use App\Entity\Transform\AbstractTransform;
use Doctrine\ORM\EntityManagerInterface;

class TransformManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTransformers(): array
    {
        return $this->entityManager->getClassMetadata(AbstractTransform::class)->discriminatorMap;
    }
}
