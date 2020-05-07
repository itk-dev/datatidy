<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Repository;

use App\Entity\DataFlow;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DataFlow|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataFlow|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataFlow[]    findAll()
 * @method DataFlow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataFlowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataFlow::class);
    }

    /**
     * @param string[] $order
     *
     * @return DataFlow[]
     */
    public function findByUser(User $user, array $order = [], int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('dataFlow')
            ->where('dataFlow.createdBy = :user')
            ->leftJoin('dataFlow.collaborators', 'data_flow_collaborators')
            ->orWhere(':user MEMBER OF dataFlow.collaborators')
            ->setParameter(':user', $user);

        foreach ($order as $sort => $direction) {
            $qb->orderBy('dataFlow.'.$sort, $direction);
        }

        $collection = new ArrayCollection($qb->getQuery()->getResult());

        return $collection->slice(0, $limit);
    }
}
