<?php

namespace App\Repository;

use App\Entity\Archive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Archive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Archive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Archive[]    findAll()
 * @method Archive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Archive::class);
    }

    public function historyFolders(Array $array = null)
    {
        $req = $this->createQueryBuilder('h')
            ->join('h.folder', 'f')
            ->addSelect('f')
            ->join('f.expertises', 'e')
            ->addSelect('e')
            ->join('e.expert', 'ex')
            ->addSelect('ex')
            ->orderBy('h.action_date', 'DESC');
        if ($array) {
            if (array_key_exists('expert', $array)) {
                $req->where('ex = :expert')
                    ->setParameter(':expert', $array['expert']);
            }
            if (array_key_exists('id', $array)) {
                $req->andWhere('f.id = :id')
                    ->setParameter('id', $array['id']);
            }
            if (array_key_exists('filters', $array)) {
                $req->andWhere('f.state LIKE :ref OR h.action_date LIKE :ref OR h.type LIKE :ref OR h.action LIKE :ref')
                    ->setParameter('ref', "%" . $array['filters'] . "%");
            }

            if (array_key_exists('start', $array) && array_key_exists('length', $array)) {
                return $req->getQuery()
                    ->setFirstResult($array['start'])
                    ->setMaxResults($array['length'])
                    ->getArrayResult();
            }
        }
        return $req->getQuery()
            ->getArrayResult();
    }

    public function countFolderArchiveExpert($id = null, $expert = null)
    {
        $req = $this->createQueryBuilder('h')
            ->join('h.folder', 'f')
            ->addSelect('f')
            ->join('f.expertises', 'e')
            ->addSelect('e')
            ->join('e.expert', 'ex')
            ->addSelect('ex')
            ->select('COUNT(h.id)');

        if ($expert) {
            $req->where('ex = :expert')
                ->setParameter(':expert', $expert);
        }
        if ($id) {
            $req->andWhere('f.id = :id')
                ->setParameter('id', $id);
        }


        return $req->getQuery()
            ->getSingleScalarResult();
    }
}
