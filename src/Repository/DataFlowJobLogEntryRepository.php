<?php

namespace App\Repository;

use App\Entity\DataFlowJobLogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DataFlowJobLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataFlowJobLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataFlowJobLogEntry[]    findAll()
 * @method DataFlowJobLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataFlowJobLogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataFlowJobLogEntry::class);
    }
}
