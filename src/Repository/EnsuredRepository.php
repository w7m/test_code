<?php

namespace App\Repository;

use App\Entity\Ensured;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ensured|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ensured|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ensured[]    findAll()
 * @method Ensured[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnsuredRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ensured::class);
    }

    // /**
    //  * @return Ensured[] Returns an array of Ensured objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ensured
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
