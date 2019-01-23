<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Paiment;
use App\Repository\ExpertiseRepository;
use App\Repository\FolderRepository;
use App\Repository\PaimentRepository;
use App\Service\FolderService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use App\Service\FilterListFolder;

/**
 * @Route("/financial", name="")
 */
class FinancialServiceController extends AbstractController
{
    /**
     * @Route("/", name="home-financial")
     * @param ExpertiseRepository $expertiseRepository
     * @param FilterListFolder $filterListFolder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ExpertiseRepository $expertiseRepository,FilterListFolder $filterListFolder)
    {
        $foldersByState = $expertiseRepository->foldersCount();
        $countFolder = $filterListFolder->countFolder($foldersByState);
        $foldersMonthsValid = $filterListFolder->foldersByStateMonths(Folder::CLOSED);
        return $this->render('insurance/sinister/skeleton_page/home.html.twig', ['folderByState' => $countFolder,

            'foldersMonthValid' => $foldersMonthsValid]);

        return $this->render('insurance/sinister/skeleton_page/home.html.twig');
    }

    /**
     * @Route("/list-folder", name="folder-sinister-financial")
     * @param FolderRepository $folderRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFolderSinister(FolderRepository $folderRepository)
    {
        $folders = $folderRepository->findBy(['state' => Folder::TO_BE_REFUND]);
        return $this->render(
           'insurance/sinister/financial_service/listFolderFinancial.html.twig' ,['folders' => $folders]
        );
    }

    /**
     * @Route("/folderDetail/{id}",name="folderDetail")
     * @param Folder $folder
     * @param ExpertiseRepository $expertiseRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function folderDetail(Folder $folder,ExpertiseRepository $expertiseRepository)
    {
        $expertise=$expertiseRepository->getToBeValidatedExpertiseFromFolder($folder->getId());
        $ensured= $folder->getVehicle()->getEnsured();
        $vehicle=$folder->getVehicle();
        return $this->render('insurance/sinister/financial_service/detailFinancialFolder.html.twig',['folder'=>$folder,'expertise'=>$expertise[0],'ensured'=>$ensured,'vehicle'=>$vehicle]);
    }

    /**
     * @Route("/folderRefundedDetail/{id}",name="folderRefundedDetail")
     * @param Folder $folder
     * @param ExpertiseRepository $expertiseRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function folderRefundedDetail(Folder $folder,ExpertiseRepository $expertiseRepository)
    {
        $expertise=$expertiseRepository->getRefundedExpertise($folder->getId());
        $ensured= $folder->getVehicle()->getEnsured();
        $vehicle=$folder->getVehicle();
        return $this->render('insurance/sinister/financial_service/detailFinancialFolder.html.twig',['folder'=>$folder,'expertise'=>$expertise[0],'ensured'=>$ensured,'vehicle'=>$vehicle]);
    }

    /**
     * @Route("/validateFolder", name="validate-refund-folder")
     * @param Registry $workflows
     * @param ObjectManager $manager
     * @param Request $request
     * @param FolderRepository $folderRepository
     * @param FolderService $folderService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateFolder(
        Registry $workflows,
        ObjectManager $manager,
        Request $request,
        FolderRepository $folderRepository,
        FolderService $folderService
    ) {
        if ($request->isXmlHttpRequest()) {
            $folderId = htmlspecialchars($_POST['folderID']);
            $folder = $folderRepository->find($folderId);
            $workflow = $workflows->get($folder);
            if ($workflow->can($folder, 'validate_refund')) {
                $workflow->apply($folder, 'validate_refund');
                $manager->flush();
                $folderService->validateEnsuredRefund($folder);
                $folderService->validateExpertsRefund($folder);
                $message = 'success';
            } else {
                $message = 'failed';
            }
            return new JsonResponse($message);
        }
        return $this->redirectToRoute('folder-sinister-financial');
    }

    /**
     * @Route("/validaExpertise",name="validaExpertise")
     * @param PaimentRepository $paimentRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPaiemnt(PaimentRepository $paimentRepository)
    {
        $paiements = $paimentRepository->findAll();

        $result=[];
        foreach ($paiements as $paiement) {
            $expertises=$paiement->getExpertises();
            $res['id']=$paiement->getId();
            $res['ref']=$paiement->getReference();
            $res['amount']=$paiement->getAmount();
            $res['date']=$paiement->getPaimentDate();
            foreach ($expertises as $expertise) {
                $res['expert'] = $expertise->getExpert();
            }
            array_push($result,$res);
        }
        return $this->render('insurance/sinister/financial_service/remboursement.html.twig', ['paiements' => $result]);
    }

    /**
     * @Route("/financialFolderDetails/{id}",name="financialFolderDetails")
     * @param Paiment $paiment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ValidateFolderDetails(Paiment $paiment)
    {
        $expertises = $paiment->getExpertises()->toArray();
        $folders = [];
        foreach ($expertises as $expertise) {
            $folders[] = ['folder'=>$expertise->getFolder(), 'honorary'=>$expertise->getHonorary()];
        }
        return $this->render('insurance/sinister/financial_service/validateFoldersDetails.html.twig', ['folders' => $folders, 'bonReference' => $paiment]);
    }
}
