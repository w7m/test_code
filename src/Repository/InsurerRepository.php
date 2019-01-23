<?php

namespace App\Repository;

use App\Entity\Insurer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Insurer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Insurer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Insurer[]    findAll()
 * @method Insurer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsurerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Insurer::class);
    }

    // /**
    //  * @return Insurer[] Returns an array of Insurer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Insurer
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
