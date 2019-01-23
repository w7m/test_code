<?php

namespace App\Controller;

use App\Entity\Archive;
use App\Entity\Folder;
use App\Entity\Paiment;
use App\Repository\ExpertiseRepository;
use App\Repository\FolderRepository;
use App\Service\ArchiveFolder;
use App\Service\FilterListFolder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expert", name="")
 */
class ExpertController extends AbstractController
{
    /**
     * @Route("/", name="expert-home")
     * @param ArchiveFolder $archiveFolder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(ArchiveFolder $archiveFolder)
    {
        $expert = $this->getUser();
        $arry = ['expert' => $expert];
        $historyFolder = $archiveFolder->historyFolderExpert($arry);
        $tenHistoryFolders = $archiveFolder->lastTenhistory($historyFolder);
        return $this->render('insurance/expert/home.html.twig', ['tenHistoryFolders' => $tenHistoryFolders]);
    }

    /**
     * @Route("/folder/{state}", name="expert-Folders",options = { "expose" = true })
     * @param Request $request
     * @param $state
     * @param FilterListFolder $filterListFolder
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function foldersByState(Request $request, $state, FilterListFolder $filterListFolder)
    {
        if ($request->isXmlHttpRequest()) {
            $expert = $this->getUser();
            $length = $request->get('length');
            $start = $request->get('start');
            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];
            $draw = $request->get('draw');
            $result = $filterListFolder->FoldersBystate($state, $filters['query'], $start, $length, $expert, $draw);
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData($result);
        }
        return $this->render('insurance/expert/foldersByStatus.html.twig', ['state' => $state]);
    }

    /**
     * @Route("/count/folder", name="count-expert-Folders",options = { "expose" = true })
     * @param Request $request
     * @param ExpertiseRepository $expertiseRepository
     * @param FilterListFolder $filterListFolder
     * @return JsonResponse
     */
    public function countFolders(
        Request $request,
        ExpertiseRepository $expertiseRepository,
        FilterListFolder $filterListFolder
    ) {
        $expert = $this->getUser();
        $jsonResponse = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $result = $expertiseRepository->foldersCount($expert);
            return $jsonResponse->setData($filterListFolder->countFolder($result));
        }
        return $jsonResponse->setData(false);
    }

    /**
     * @Route("/folders/by/month", name="count-expert-Folders-month",options = { "expose" = true })
     * @param Request $request
     * @param ExpertiseRepository $expertiseRepository
     * @param FilterListFolder $filterListFolder
     * @return JsonResponse
     */
    public function foldersCurrentMonth(
        Request $request,
        ExpertiseRepository $expertiseRepository,
        FilterListFolder $filterListFolder
    ) {
        $jsonResponse = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $expert = $this->getUser();
            $result = $expertiseRepository->foldersCurrentMonth($expert);
            $data = $filterListFolder->countFolder($result);
            return $jsonResponse->setData($data);
        }
        return $jsonResponse->setData(false);
    }

    /**
     * @Route("/history-folder/{id}", name="history-folder",options = { "expose" = true })
     * @param Request $request
     * @param ArchiveFolder $archiveFolder
     * @param $id
     * @param FolderRepository $folderRepository
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function historyFolder(Request $request, ArchiveFolder $archiveFolder, $id, FolderRepository $folderRepository)
    {
        $refFolder = $folderRepository->find($id)->getRef();
        if ($request->isXmlHttpRequest()) {
            $expert = $this->getUser();
            $length = $request->get('length');
            $start = $request->get('start');
            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];
            $draw = $request->get('draw');
            $array = ['expert' => $expert,
                'length' => $length,
                'start' => $start,
                'filters' => $filters['query'],
                'id' => $id];
            $data = $archiveFolder->historyFolderExpert($array);
            $result = $archiveFolder->dataHistoryForDataTable($data, $draw, $id, $expert);
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData($result);
        }
        return $this->render('insurance/expert/historyFolder.html.twig', ['reference' => $refFolder]);
    }

    /**
     * @Route("/history-folder-detail/{id}", name="history-folder-detail")
     * @param Archive $archive
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historydetail(Archive $archive)
    {
        return $this->render('insurance/expert/detailhistoryFolder.html.twig', ['archive' => $archive]);
    }

    /**
     * @Route("/folder-detail-closed/{id}", name="history-folder-detail-closed-to-be-resigned")
     * @param Folder $folder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailFolderClosedToBeResigned(Folder $folder)
    {
        return $this->render('insurance/sinister/validator_teams/detailsFolderSinister.html.twig', ['folder' => $folder]);
    }

    /**
     * @Route("/myPaiement",name="myPaiement")
     * @param ExpertiseRepository $expertiseRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myPaiement(ExpertiseRepository $expertiseRepository)
    {
        $expert = $this->getUser();
        $expertise=$expertiseRepository->getRefundedExpertiseByExpert($expert->getId());
        return $this->render('insurance/expert/myPaiement.html.twig',['expertises'=>$expertise]);

    }
    /**
     * @Route("/paiementDetail/{id}",name="paiementDetail")
     */
    public function paiementDetail(Paiment $paiment)
    {
        $expertises = $paiment->getExpertises()->toArray();
        $folders = [];
        foreach ($expertises as $expertise) {
            $folders[] = ['folder'=>$expertise->getFolder(), 'honorary'=>$expertise->getHonorary()];
        }
        return $this->render('insurance/expert/paiementDetail.html.twig',['folders'=>$folders]);
    }
}
