<?php
/**
 * Created by PhpStorm.
 * User: ebensaid
 * Date: 11/01/2019
 * Time: 12:43
 */

namespace App\Service;


use App\Entity\Parameter;
use App\Repository\ParameterRepository;

class ParameterService
{
    private $parameter;

    /**
     * ParameterService constructor.
     * @param ParameterRepository $parameterRepository
     */
    public function __construct(ParameterRepository $parameterRepository)
    {
        $this->parameter =$parameterRepository->findAll()[0];
    }

    public function getParametersToHonoraryParameters()
    {
        return [
            'photoPrice'=>$this->parameter->getPhotoPrice(),
            'openingFileExpense'=>$this->parameter->getOpeningFileExpense(),
            'expertiseFees'=>$this->parameter->getExpertiseFees(),
            'billPercentage'=>$this->parameter->getBillPercentage(),
        ];
    }
}