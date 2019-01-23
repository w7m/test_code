<?php

namespace App\Repository;

use App\Entity\WreckageReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WreckageReport|null find($id, $lockMode = null, $lockVersion = null)
 *  @method WreckageReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method WreckageReport[]    findAll()
 * @method WreckageReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WreckageReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WreckageReport::class);
    }

    // /**
    //  * @return WreckageReport[] Returns an array of WreckageReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WreckageReport
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
