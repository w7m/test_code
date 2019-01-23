<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 07/01/2019
 * Time: 17:25
 */

namespace App\Service;

use App\Entity\Insurer;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class FinancierDataTable extends AbstractDataTableHandler

{
    const ID = "financier";
    protected $doctrine;
    protected $router;

    public function __construct(RegistryInterface $doctrine,RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router=$router;

    }


    public function handle(DataTableQuery $request): DataTableResults
    {

        $repository = $this->doctrine->getRepository(Insurer::class);
        //Total number of experts
        $query = $repository->createQueryBuilder('i');

        $results = new DataTableResults();

        $results->recordsTotal = $query->select('COUNT(i.id)')->getQuery()->getSingleScalarResult();
        $query->select('i.id','i.insurerId','i.username', 'i.firstName', 'i.lastName', 'i.phoneNumber', 'i.email')
            ->where(' i.roles LIKE :role')
            ->setParameter('role', '%ROLE_FINANCIAL%');
        /** @var \App\Entity\Insurer[] $agents */

        //search
        if ($request->search->value) {
            $query->andWhere(
                '(
                LOWER(i.insurerId)LIKE :search OR' . '
                LOWER(i.firstName)LIKE :search OR' . '
                LOWER(i.lastName) LIKE :search  OR ' . '
                LOWER(i.phoneNumber) LIKE :search OR' . '
                LOWER(i.email) LIKE :search
                )'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        // Order.
        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('i.insurerId', $order->dir);
            }
            elseif  ($order->column == 1) {
                $query->addOrderBy('i.firstName', $order->dir);
            } elseif  ($order->column == 2) {
                $query->addOrderBy('i.lastName', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('i.phoneNumber', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('i.email', $order->dir);

            }
        }
        // Get filtered count.
        $queryCount = clone $query;
        $queryCount->select('COUNT(i.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        // Restrict results.
        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);
        $agents = $query->getQuery()->getResult();
        foreach ($agents as $agent) {
            $agentDetails = $this->router->generate('expertDetails', ["id" => $agent['id']]);
            $agentUpdate = $this->router->generate('agentUpdate', ["id" => $agent['id']]);

            $results->data[] = [

               $agent['firstName'], $agent['lastName'], $agent['phoneNumber'], $agent['email'],

                '<a class=\'btn btn-info \' href="' . $agentDetails . '" title="details"> <span class=\'fa fa-info-circle\' ></span></a>
                 <a class=\'btn btn-primary\' href="' . $agentUpdate . '" title="modifier"> <span class="fa fa-edit" ></span></a>'
            ];
        }
        return $results;
    }
}
