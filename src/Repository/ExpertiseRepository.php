<?php

namespace App\Repository;

use App\Entity\Expertise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Expertise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expertise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expertise[]    findAll()
 * @method Expertise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertiseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Expertise::class);
    }

    public function ExpertiseByStatee($state, $filters, $start, $length, $expert)
    {
        $req = $this->createQueryBuilder('e')
            ->join('e.folder', 'f')
            ->addSelect('f')
            ->join('f.vehicle', 'v')
            ->addSelect('v')
            ->join('v.ensured', 'en')
            ->addSelect('en')
            ->Where('f.state = :state')
            ->setParameter('state', $state)
            ->andWhere('e.expert= :expert')
            ->setParameter('expert', $expert)
            ->groupBy('e.folder')
            ->orderBy('e.assignmentDate', 'DESC');
        if ($filters != "") {
            $req->andWhere('f.ref LIKE :ref OR v.registrationNumber LIKE :ref OR en.firstName  LIKE :ref OR en.lastName  LIKE :ref')
                ->setParameter('ref', "%" . $filters . "%");
        }
        return $req->getQuery()
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->getArrayResult();
    }


    public function foldersCurrentMonth($expert)
    {
        return $this->createQueryBuilder('e')
            ->join('e.folder', 'f')
            ->addSelect('f')
            ->Where('e.expert= :expert')
            ->setParameter('expert', $expert)
            ->andWhere('MONTH(e.assignmentDate) = MONTH(CURRENT_TIMESTAMP())')
            ->andWhere('YEAR(e.assignmentDate) = YEAR(CURRENT_TIMESTAMP())')
            ->select('f.id as id,f.state')
            ->getQuery()
            ->getResult();
    }

    public function foldersCount($expert = null, $state = null)
    {
        $req = $this->createQueryBuilder('e')
            ->join('e.folder', 'f')
            ->addSelect('f');


        if ($expert) {
            $req->where('e.expert= :expert')
                ->setParameter('expert', $expert);

        }
        if ($state) {
            $req->AndWhere('f.state= :state')
                ->setParameter('state', $state);
        }

        return $req->select('f.id as id,f.state')
            ->getQuery()
            ->getResult();
    }


    public function getValidatedExpertise($expert)
    {
        return $this->createQueryBuilder('e')
            ->where('e.expert=:expert')
            ->setParameter('expert', $expert)
            ->andWhere('e.refundStatus=:refundState')
            ->setParameter('refundState', 'validated')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $expert
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalHonorary($expert)
    {
        return $this->createQueryBuilder('e')
            ->where('e.expert=:expert')
            ->setParameter('expert', $expert)
            ->andWhere('e.refundStatus=:refundState')
            ->setParameter('refundState', 'validated')
            ->select('SUM(e.honorary)')
            ->getQuery()
            ->getOneOrNullResult();

    }
    public function getToBeValidatedExpertiseFromFolder($folderId)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.folder', 'folder')
            ->addSelect('folder')
            ->where('e.refundStatus=:refundState')
            ->setParameter('refundState', 'waiting-validation')
            ->andWhere('folder.id=:id')
            ->setParameter('id', $folderId)
            ->getQuery()
            ->getResult();
    }
    public function getRefundedExpertise($folderId)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.folder', 'folder')
            ->addSelect('folder')
            ->where('e.refundStatus=:refundState')
            ->setParameter('refundState', 'refunded')
            ->andWhere('folder.id=:id')
            ->setParameter('id', $folderId)
            ->getQuery()
            ->getResult();
    }

    public function getRefundedExpertiseByExpert($expert)
    {
        return $this->createQueryBuilder('e')
            ->where('e.expert=:expert')
            ->setParameter('expert',$expert)
            ->andWhere('e.refundStatus=:refundState')
            ->setParameter('refundState','refunded')
            ->getQuery()
            ->getResult();
    }


}
