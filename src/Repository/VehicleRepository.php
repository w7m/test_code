<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findOneByInsured($value)
    {
        return $this->createQueryBuilder('v')
            ->Join('v.ensured', 'e')
            ->addSelect('e')
            ->andWhere('v.registrationNumber = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getArrayResult();
    }

    public function totalVehicles()
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->getQuery()->getSingleScalarResult();
    }

    public function vehicleMonths()
    {
        return $this->createQueryBuilder('v')
            ->Where('MONTH(v.dateOfRegistration) = 1
             OR MONTH(v.dateOfRegistration)=2 
             OR MONTH(v.dateOfRegistration)=3 
             OR MONTH(v.dateOfRegistration)=4 
             OR MONTH(v.dateOfRegistration)=5 
             OR MONTH(v.dateOfRegistration)=6 
             OR MONTH(v.dateOfRegistration)=7 
             OR MONTH(v.dateOfRegistration)=8 
             OR MONTH(v.dateOfRegistration)=9 
             OR MONTH(v.dateOfRegistration)=10 
             OR MONTH(v.dateOfRegistration)=11 
             OR MONTH(v.dateOfRegistration)=12')
            ->andWhere('YEAR(v.dateOfRegistration) = YEAR(CURRENT_TIMESTAMP())')
            ->select('MONTH(v.dateOfRegistration) as month,COUNT(v)')
            ->groupBy('month')
            ->getQuery()
            ->getScalarResult();
    }


}
