<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 07/01/2019
 * Time: 17:24
 */

namespace App\Service;

use App\Repository\ExpertRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class ExpertChoiceList
{
    protected $doctrine;
    protected $router;
    protected $expertRepository;

    public function __construct(RegistryInterface $doctrine, RouterInterface $router, ExpertRepository $expertRepository)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->expertRepository = $expertRepository;
    }
    public function expertToRessignList($experts, $filters, $start, $length,$draw)
    {
        $data = $this->expertRepository->expertToRessign($experts, $filters, $start, $length);
        $numberExperts = count($this->expertRepository->allExperts());
        $data1 = [];
        foreach ($data as $value) {
            $value['selection'] = '<div class="input-group rodioDiv">
                 <input type="radio" class="radiInput" name="expertId" value="'.$value['id'].'"></div>';
            $data1[] = $value;
        }
        $result = [
            'draw' => $draw,
            "recordsFiltered" => $numberExperts,
            "recordsTotal" => $numberExperts,
            "aaData" => $data1
        ];
        return $result;
    }

}
