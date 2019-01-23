<?php
/**
 * Created by PhpStorm.
 * User: ebensaid
 * Date: 12/01/2019
 * Time: 16:52
 */

namespace App\Service;


use App\Entity\Folder;

class BillsService
{
    public function getTotalAmountBills(Folder $folder)
    {
        $total = 0;
        $bills= $folder->getBills();
        foreach ($bills as $bill) {
            $total += $bill->getRealAmount();
        }
        return $total;
    }
}