<?php

namespace App\Repository;

use App\Entity\DataFlowJob;
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

    public function findAll(): array
    {
        return $this->findBy([], ['startedAt' => 'desc']);
    }
}
