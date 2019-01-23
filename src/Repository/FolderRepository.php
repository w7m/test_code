<?php

namespace App\Repository;

use App\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Folder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Folder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Folder[]    findAll()
 * @method Folder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Folder::class);

    }

    public function foldersByStateMonths($sate = null)
    {
        $req=  $this->createQueryBuilder('f')
            ->innerJoin('f.expertises', 'exp')
            ->addSelect('exp');
        if ($sate){
            $req->where('f.state=:state')
                ->setParameter('state', $sate);
        }

            $req->andWhere('MONTH(exp.assignmentDate) = 1
             OR MONTH(exp.assignmentDate)=2 
             OR MONTH(exp.assignmentDate)=3 
             OR MONTH(exp.assignmentDate)=4 
             OR MONTH(exp.assignmentDate)=5 
             OR MONTH(exp.assignmentDate)=6 
             OR MONTH(exp.assignmentDate)=7 
             OR MONTH(exp.assignmentDate)=8 
             OR MONTH(exp.assignmentDate)=9 
             OR MONTH(exp.assignmentDate)=10 
             OR MONTH(exp.assignmentDate)=11 
             OR MONTH(exp.assignmentDate)=12')
            ->andWhere('YEAR(exp.assignmentDate) = YEAR(CURRENT_TIMESTAMP())')
            ->select('MONTH(exp.assignmentDate) as month,COUNT(f)')
            ->groupBy('month');
            return $req->getQuery()
            ->getScalarResult();
    }
    public function totlaFoldes()
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->getQuery()->getSingleScalarResult();
    }



}
