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
use App\Entity\DataFlowJob;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;

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
     * @param array $order
     * @param int $limit
     * @return DataFlowJob[]
     */
    public function findByUser(User $user, array $order = [], int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('dataFlowJob')
            ->leftJoin('dataFlowJob.dataFlow', 'dataFlow')
            ->andWhere('dataFlow.createdBy = :user')
            ->leftJoin('dataFlow.collaborators', 'data_flow_collaborators')
            ->orWhere(':user MEMBER OF dataFlow.collaborators')
            ->setParameter(':user', $user);

        foreach ($order as $sort => $direction) {
            $qb->orderBy('dataFlowJob.'.$sort, $direction);
        }

        $collection = new ArrayCollection($qb->getQuery()->getResult());

        return $collection->slice(0, $limit);
    }

    /**
     * @return DataFlowJob[]
     */
    public function findAllNonComplete(): array
    {
        return $this->createQueryBuilder('dataFlowJob')
            ->andWhere('status != :status')
            ->setParameter(':status', DataFlowJob::STATUS_COMPLETED)
            ->getQuery()
            ->execute();
    }

    /**
     * @return DataFlow[]
     */
    public function findActiveJobsByDataFlow(DataFlow $dataFlow): array
    {
        return $this->createQueryBuilder('dataFlowJob')
            ->leftJoin('dataFlowJob.dataFlow', 'dataFlow')
            ->where('dataFlowJob.dataFlow = :dataFlow')
            ->setParameter(':dataFlow', $dataFlow)
            ->andWhere('dataFlowJob.status NOT IN (:statuses)')
            ->setParameter(':statuses', [DataFlowJob::STATUS_COMPLETED, DataFlowJob::STATUS_FAILED, DataFlowJob::STATUS_CANCELLED])
            ->getQuery()
            ->execute();
    }
}
