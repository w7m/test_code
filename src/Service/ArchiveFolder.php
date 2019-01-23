<?php
/**
 * Created by PhpStorm.
 * User: wmhamdi
 * Date: 09/01/2019
 * Time: 14:28
 */

namespace App\Service;


use App\Repository\ArchiveRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArchiveFolder
{

    private $archiveRepository;
    private $router;

    /**
     * HistoryFolder constructor.
     * @param $repositoryHistory
     */
    public function __construct(UrlGeneratorInterface $router, ArchiveRepository $archiveRepository)
    {
        $this->archiveRepository = $archiveRepository;
        $this->router = $router;
    }

    public function historyFolderExpert(Array $array=null)
    {
        return $this->archiveRepository->historyFolders($array);
    }

    public function dataHistoryForDataTable($data, $draw, $id, $expert)
    {
        $count = $this->archiveRepository->countFolderArchiveExpert($id, $expert);
        $data1 = [];
        foreach ($data as $value) {
            $value['action_date'] = $value['action_date']->format('Y-m-d');
            $urlHistoryDetail = $this->router->generate(
                'history-folder-detail',
                array('id' => $value['id'])
            );
            $value['detailFolder'] = "<div class='d-flex justify-content-sm-between'><a href='" . $urlHistoryDetail . "' class='btn btn-primary'>Détail <span class='fa fa-info-circle'></span></a></div>";
            $data1[] = $value;
        }
        $result = [
            "draw" => $draw,
            "recordsFiltered" => $count,
            "recordsTotal" => $count,
            "aaData" => $data1
        ];
        return $result;
    }

    public function lastTenhistory($data)
    {
        return $output = array_slice($data, 0, 10);
    }

}