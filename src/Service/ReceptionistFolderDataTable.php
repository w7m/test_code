<?php
/**
 * Created by PhpStorm.
 * User: jghada
 * Date: 10/01/2019
 * Time: 17:38
 */

namespace App\Service;


use App\Entity\Expert;
use App\Entity\Folder;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class ReceptionistFolderDataTable extends AbstractDataTableHandler
{
    const ID = 'folder';
    protected $doctrine;
    protected $router;

    public function __construct(RegistryInterface $doctrine, RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Folder::class);
        $query = $repository->createQueryBuilder('f');
        $results = new DataTableResults();
        $results->recordsTotal = $query->select('COUNT(f.id)')->getQuery()->getSingleScalarResult();
        $query->select('f.id', 'f.ref', 'vehicle.registrationNumber')->join('f.vehicle', 'vehicle')
            ->join('f.expertises','expertises')
            ->join('expertises.expert','expert')
            ->where('f.state =:state')
            ->setParameter('state','created');



        //search
        if ($request->search->value) {
            $query->andWhere(
                '(
                LOWER(f.state)LIKE :search OR' . '
                LOWER(f.ref)LIKE :search OR' . '           
                LOWER(vehicle.registrationNumber) LIKE :search
                )'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        // Order.
        foreach ($request->order as $order) {

            if ($order->column == 0) {
                $query->addOrderBy('f.ref', $order->dir);
            }
        }
        $queryCount = clone $query;
        $queryCount->select('COUNT(f.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        // Restrict results.
        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);
        $folders = $query->getQuery()->getResult();


        foreach ($folders as $folder) {

            $folderDetails = $this->router->generate('RecepfolderDetails', ["id" => $folder['id']]);
            $results->data[] = [
                $folder['ref'],$folder['ref'], $folder['registrationNumber'],
                ' <a id="btn-expert-detail" class=\'btn btn-info \' href="' . $folderDetails . '" title="details"> <span class=\'fa fa-info-circle\' ></span></a>'
            ];
        }
        return $results;
    }

}