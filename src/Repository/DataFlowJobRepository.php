<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Repository;

use App\Entity\DataFlowJob;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DataFlowJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataFlowJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataFlowJob[]    findAll()
 * @method DataFlowJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataFlowJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataFlowJob::class);
    }

    /**
     * Fetches all data flow jobs that the user is either the creator or collaborator of.
     *
     * @param User $user
     * @param string $order
     * @return DataFlowJob[]
     */
    public function findByUser(User $user, string $order = 'DESC'): array
    {
        return $this->createQueryBuilder('dataFlowJob')
            ->leftJoin('dataFlowJob.dataFlow', 'dataFlow')
            ->andWhere('dataFlow.createdBy = :user')
            ->leftJoin('dataFlow.collaborators', 'data_flow_collaborators')
            ->orWhere(':user MEMBER OF dataFlow.collaborators')
            ->setParameter(':user', $user)
            ->orderBy('dataFlowJob.startedAt', ':order')
            ->setParameter(':order', $order)
            ->getQuery()
            ->execute();
    }
}
