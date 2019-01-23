<?php
/**
 * Created by PhpStorm.
 * User: jghada
 * Date: 11/01/2019
 * Time: 11:19
 */

namespace App\Service;

use App\Entity\Expert;
use App\Entity\Folder;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class ValidatorFolderDataTable extends AbstractDataTableHandler
{
    const ID = 'validatorFolder';
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
        $query->select('f.id','f.state','f.ref', 'vehicle.registrationNumber')
            ->join('f.vehicle', 'vehicle')
            ->join('f.expertises','expertises')
            ->where('f.state IN (:state) ')
            ->setParameters(array('state'=>['submitted','wreck-report-sent']));



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
        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('f.ref', $order->dir);
            }
            elseif  ($order->column == 1) {
                $query->addOrderBy('f.state', $order->dir);
            } elseif  ($order->column == 2) {
                $query->addOrderBy('vehicle.registrationNumber', $order->dir);
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

            $folderDetails = $this->router->generate('folder-details', ["id" => $folder['id']]);
if($folder['state']=="wreck-report-sent") {
    $type = 'épave';
}elseif ( $folder['state']=="submitted") {
    $type = 'normal';
}

            $results->data[] = [
                $folder['ref'],$type,$folder['registrationNumber'],
                ' <a id="btn-expert-detail" class=\'btn btn-info \' href="' . $folderDetails . '" title="details"> <span class=\'fa fa-info-circle\' ></span></a>'

            ];
        }
        return $results;
    }

}