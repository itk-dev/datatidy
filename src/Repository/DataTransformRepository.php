<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Repository;

use App\Entity\AbstractDataTransform;
use App\Entity\DataTransform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AbstractDataTransform|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractDataTransform|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractDataTransform[]    findAll()
 * @method AbstractDataTransform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataTransformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataTransform::class);
    }

    // /**
    //  * @return AbstractDataTransform[] Returns an array of AbstractDataTransform objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AbstractDataTransform
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
