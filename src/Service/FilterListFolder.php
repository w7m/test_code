<?php
/**
 * Created by PhpStorm.
 * User: wmhamdi
 * Date: 07/01/2019
 * Time: 08:41
 */

namespace App\Service;


use App\Entity\Folder;
use App\Repository\ExpertiseRepository;
use App\Repository\FolderRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FilterListFolder
 * @package App\Service
 */
class FilterListFolder
{

    private $router;
    private $expertiseRepository;
    private $folderRepository;

    public function __construct(UrlGeneratorInterface $router, ExpertiseRepository $expertiseRepository, FolderRepository $folderRepository)
    {
        $this->router = $router;
        $this->expertiseRepository = $expertiseRepository;
        $this->folderRepository = $folderRepository;
    }


    /**
     * @param $data
     * @param $totalFolder
     * @param $draw
     * @return array
     * Méthode générée un tableau formaté pour le dataTable
     */
    public function FoldersBystate($state, $filters, $start, $length, $expert, $draw)
    {

        $data = $this->expertiseRepository->ExpertiseByStatee($state, $filters, $start, $length, $expert);
        $numberFolder = count($this->expertiseRepository->foldersCount($expert, $state));

        $data1 = [];
        foreach ($data as $value) {
            $value['assignmentDate'] = $value['assignmentDate']->format('Y-m-d');
            $value['client'] = $value['folder']['vehicle']['ensured']['firstName'] . " " . $value['folder']['vehicle']['ensured']['lastName'];
            $urlDetail = $this->router->generate(
                'editFolder',
                array('id' => $value['folder']['id'])
            );
            $urlHistory = $this->router->generate(
                'history-folder',
                array('id' => $value['folder']['id'])
            );
            if ($state === Folder::CLOSED) {
                $urlDetail = $this->router->generate(
                    'history-folder-detail-closed-to-be-resigned',
                    array('id' => $value['folder']['id'])
                );
            }
            $value['action'] = "<div class='d-flex justify-content-around '><a  href='" . $urlDetail . "' class='btn btn-primary btn-sm'>Détail <span class='fa fa-info-circle'></span></a> <a href='" . $urlHistory . "' class='btn btn-primary btn-sm'>Historique <span class='fa fa-history'></span></a></div>";
            $data1[] = $value;
        }
        $result = [
            'draw' => $draw,
            "recordsFiltered" => $numberFolder,
            "recordsTotal" => $numberFolder,
            "aaData" => $data1
        ];
        return $result;
    }

    /**
     * @param $array
     * @return array
     */
    public function countFolder($array)
    {
        $arrayResult = ["created" => 0,
            "inProgress" => 0,
            "toBeReconsidered" => 0,
            "reassigned" => 0,
            "sellingStandby" => 0,
            "closed" => 0,
            "WreckReportSent" => 0,
            "submitted" => 0,
            "toBeRefended" => 0
        ];

        foreach ($array as $value) {
            if ($value['state'] === Folder::CREATED) {
                $arrayResult['created'] = $arrayResult['created'] + 1;
            } elseif ($value['state'] === Folder::IN_PROGRESS) {
                $arrayResult['inProgress'] = $arrayResult['inProgress'] + 1;
            } elseif ($value['state'] === Folder::TO_BE_RECONSEDERED) {
                $arrayResult['toBeReconsidered'] = $arrayResult['toBeReconsidered'] + 1;
            } elseif ($value['state'] === Folder::REASSIGNED) {
                $arrayResult['reassigned'] = $arrayResult['reassigned'] + 1;
            } elseif ($value['state'] === Folder::SELLING_STANDBY) {
                $arrayResult['sellingStandby'] = $arrayResult['sellingStandby'] + 1;
            } elseif ($value['state'] === Folder::CLOSED) {
                $arrayResult['closed'] = $arrayResult['closed'] + 1;
            } elseif ($value['state'] === Folder::SUBMITTED) {
                $arrayResult['submitted'] = $arrayResult['submitted'] + 1;

            } elseif ($value['state'] === Folder::WRECK_REPORT_SENT) {
                $arrayResult['WreckReportSent'] = $arrayResult['WreckReportSent'] + 1;

            } elseif ($value['state'] === Folder::TO_BE_REFUND) {
                $arrayResult['toBeRefended'] = $arrayResult['toBeRefended'] + 1;
            } else {
                false;
            }
        }
        return $arrayResult;
    }

    public function foldersByStateMonths($state=null)
    {
        $foldersMonths = ['january' => 0,
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
        $result = $this->folderRepository->foldersByStateMonths($state);
        foreach ($result as $value){
            if ($value['month']==='1'){
               $foldersMonths['january']=$value[1];
            } elseif ($value['month']==='2'){
                $foldersMonths['february']=$value[1];
            } elseif ($value['month']==='3'){
                $foldersMonths['march']=$value[1];
            } elseif ($value['month']==='4'){
                $foldersMonths['april']=$value[1];
            } elseif ($value['month']==='5'){
                $foldersMonths['may']=$value[1];
            } elseif ($value['month']==='6'){
                $foldersMonths['june']=$value[1];
            } elseif ($value['month']==='7'){
                $foldersMonths['july']=$value[1];
            } elseif ($value['month']==='8'){
                $foldersMonths['august']=$value[1];
            } elseif ($value['month']==='9'){
                $foldersMonths['september']=$value[1];
            } elseif ($value['month']==='10'){
                $foldersMonths['october']=$value[1];
            } elseif ($value['month']==='11'){
                $foldersMonths['november']=$value[1];
            } elseif ($value['month']==='12'){
                $foldersMonths['december']=$value[1];
            } else {
                false;
            }
        }
        return $foldersMonths;
    }
}