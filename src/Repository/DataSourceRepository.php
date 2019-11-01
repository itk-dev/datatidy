<?php

namespace App\Repository;

use App\Entity\AbstractDataSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AbstractDataSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractDataSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractDataSource[]    findAll()
 * @method AbstractDataSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractDataSource::class);
    }
}
