<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 07/01/2019
 * Time: 17:24
 */

namespace App\Service;

use App\Entity\Expert;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class ExpertChoiceDataTable extends AbstractDataTableHandler
{
    const ID = 'experts_choice';
    protected $doctrine;
    protected $router;


    public function __construct(RegistryInterface $doctrine, RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }


    public function handle(DataTableQuery $request): DataTableResults
    {

        $repository = $this->doctrine->getRepository(Expert::class);
        //Total number of experts
        $query = $repository->createQueryBuilder('e');

        $results = new DataTableResults();

        $results->recordsTotal = $query->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $query->select('e.id','e.firstName', 'e.lastName', 'e.phoneNumber', 'e.email', 'e.registration_tax_number', 'e.company_name');

        /** @var \App\Entity\Expert[] $experts */
        //search
        if ($request->search->value) {
            $query->where(
                '(
                LOWER(e.firstName)LIKE :search OR' . '
                 LOWER(e.lastName) LIKE :search  OR ' . '
                LOWER(e.phoneNumber) LIKE :search OR' . '
                LOWER(e.email) LIKE :search OR' . '
                LOWER(e.registration_tax_number) LIKE :search OR ' . '
                LOWER(e.company_name) LIKE :search
                )'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }
        // Order.
        foreach ($request->order as $order) {


            // "firstName" column
            if ($order->column == 0) {
                $query->addOrderBy('e.firstName', $order->dir);
            } // "lastName" column
            elseif ($order->column == 1) {
                $query->addOrderBy('e.lastName', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('e.phoneNumber', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('e.email', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('e.registration_tax_number', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('e.company_name', $order->dir);
            }
        }
        // Get filtered count.
        $queryCount = clone $query;
        $queryCount->select('COUNT(e.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        // Restrict results.
        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);
        $experts = $query->getQuery()->getResult();
        foreach ($experts as $expert) {
            $results->data[] = [
                '<div class="input-group">
                 <input type="radio" name="expertId" value="'.$expert['id'].'"></div>',
                $expert['firstName'], $expert['lastName'], $expert['phoneNumber'], $expert['email'], $expert['registration_tax_number'], $expert['company_name']

            ];
        }
        return $results;
    }
}
