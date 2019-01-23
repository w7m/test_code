<?php
/**
 * Created by PhpStorm.
 * User: Arfaoui Slim
 * Date: 14/01/2019
 * Time: 22:20
 */

namespace App\Service;


use App\Entity\Vehicle;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class ReceptionistContractDataTable extends AbstractDataTableHandler
{

    const ID = 'contract';
    protected $doctrine;
    protected $router;

    /**
     * ReceptionistContractDataTable constructor.
     * @param RegistryInterface $doctrine
     * @param RouterInterface $router
     */
    public function __construct(RegistryInterface $doctrine, RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * Handles specified DataTable request.
     *
     * @param DataTableQuery $request
     *
     * @throws DataTableException
     *
     * @return DataTableResults
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Vehicle::class);
        $query = $repository->createQueryBuilder('v');
        $results = new DataTableResults();
        $results->recordsTotal = $query->select('COUNT(v.id)')->getQuery()->getSingleScalarResult();
        $query->select('v.id', 'v.type','v.registrationNumber','v.dateOfRegistration', 'ensured.firstName','ensured.lastName')
            ->join('v.ensured', 'ensured');

        if ($request->search->value) {
            $query->Where(
                '(
                LOWER(v.type)LIKE :search OR' . '
                LOWER(v.registrationNumber)LIKE :search OR' . '           
                LOWER(v.dateOfRegistration)LIKE :search OR' . '           
                LOWER(ensured.firstName)LIKE :search OR' . '           
                LOWER(ensured.lastName) LIKE :search
                )'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('v.type', $order->dir);
            }
            elseif  ($order->column == 1) {
                $query->addOrderBy('v.registrationNumber', $order->dir);
            }
            elseif  ($order->column == 2) {
                $query->addOrderBy('v.dateOfRegistration', $order->dir);
            }
            elseif  ($order->column == 3) {
                $query->addOrderBy('ensured.firstName', $order->dir);
            }
            elseif  ($order->column == 4) {
                $query->addOrderBy('ensured.lastName', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(v.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);
        $vehicules = $query->getQuery()->getResult();

        foreach ($vehicules as $vehicule) {
            $dateRegistration = $vehicule['dateOfRegistration']->format('d/m/Y');
            $vehiculeDetails = $this->router->generate('RecepContractDetails', ["id" => $vehicule['id']]);
            $results->data[] = [
                $vehicule['type'],$vehicule['registrationNumber'], $dateRegistration, $vehicule['firstName'], $vehicule['lastName'],
                ' <a id="btn-expert-detail" class=\'btn btn-info \' href="' . $vehiculeDetails . '" title="details"> <span class=\'fa fa-info-circle\' ></span></a>'
            ];
        }
        return $results;


    }
}