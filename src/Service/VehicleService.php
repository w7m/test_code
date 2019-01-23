<?php
/**
 * Created by PhpStorm.
 * User: wmhamdi
 * Date: 15/01/2019
 * Time: 14:08
 */

namespace App\Service;


use App\Repository\VehicleRepository;

class VehicleService
{
    private $vehicleRepository;

    /**
     * VehicleService constructor.
     * @param $vehicleRepository
     */
    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function vehicleByeMonths()
    {
        $vehicleMonths = ['january' => 0,
            'february' => 0,
            'march' => 0,
            'april' => 0,
            'may' => 0,
            'june' => 0,
            'july' => 0,
            'august' => 0,
            'september' => 0,
            'october' => 0,
            'november' => 0,
            'december' => 0];
        $result = $this->vehicleRepository->vehicleMonths();
        foreach ($result as $value){
            if ($value['month']==='1'){
                $vehicleMonths['january']=$value[1];
            } elseif ($value['month']==='2'){
                $vehicleMonths['february']=$value[1];
            } elseif ($value['month']==='3'){
                $vehicleMonths['march']=$value[1];
            } elseif ($value['month']==='4'){
                $vehicleMonths['april']=$value[1];
            } elseif ($value['month']==='5'){
                $vehicleMonths['may']=$value[1];
            } elseif ($value['month']==='6'){
                $vehicleMonths['june']=$value[1];
            } elseif ($value['month']==='7'){
                $vehicleMonths['july']=$value[1];
            } elseif ($value['month']==='8'){
                $vehicleMonths['august']=$value[1];
            } elseif ($value['month']==='9'){
                $vehicleMonths['september']=$value[1];
            } elseif ($value['month']==='10'){
                $vehicleMonths['october']=$value[1];
            } elseif ($value['month']==='11'){
                $vehicleMonths['november']=$value[1];
            } elseif ($value['month']==='12'){
                $vehicleMonths['december']=$value[1];
            } else {
                false;
            }
        }
        return $vehicleMonths;
    }

}