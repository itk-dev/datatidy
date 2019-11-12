<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Repository;

use App\Entity\DataTarget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DataTarget|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataTarget|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataTarget[]    findAll()
 * @method DataTarget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataTargetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataTarget::class);
    }
}
