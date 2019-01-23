<?php

namespace App\Repository;

use App\Entity\Expert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Expert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expert[]    findAll()
 * @method Expert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Expert::class);
    }

    /**
     * @param $value
     * @return Expert[] Returns an array of Expert objects
     */

    public function findByExpert($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.registration_tax_number = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function expertToRessign($experts, $filters, $start, $length)
    {
        $req = $this->createQueryBuilder('e')
            ->select('e');
            $i = 0;
            foreach($experts as $expert)
            {

                $req->andWhere('e.id != :expertId'.$i)
                    ->setParameter('expertId'.$i, $expert);
                $i++;
            }
        if ($filters != "") {
            $req->andWhere('e.email LIKE :ref OR e.registration_tax_number LIKE :ref OR e.company_name  LIKE :ref OR e.lastName LIKE :ref OR e.firstName LIKE :ref OR e.phoneNumber LIKE :ref')
                ->setParameter('ref', "%" . $filters . "%");
        }
        return $req->getQuery()
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->getArrayResult();
    }
    public function allExperts()
    {
        $req = $this->createQueryBuilder('e')
            ->select('e');
        return $req->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Expert
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
