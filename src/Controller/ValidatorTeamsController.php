<?php

namespace App\Controller;

use App\Entity\AttachedFile;
use App\Entity\Folder;
use App\Repository\ExpertiseRepository;
use App\Repository\ExpertRepository;
use App\Repository\FolderRepository;
use App\Service\AttachedFilesService;
use App\Service\ExpertChoiceList;
use App\Service\ExpertiseService;
use App\Service\FilterListFolder;
use App\Service\FolderService;
use DataTables\DataTablesInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/validator", name="")
 */
class ValidatorTeamsController extends AbstractController
{
    /**
     * @Route("/", name="home-validator_teams")
     * @param ExpertiseRepository $expertiseRepository
     * @param FilterListFolder $filterListFolder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ExpertiseRepository $expertiseRepository, FilterListFolder $filterListFolder)
    {
        $foldersByState = $expertiseRepository->foldersCount();
        $countFolder = $filterListFolder->countFolder($foldersByState);
        $foldersMonthsValide = $filterListFolder->foldersByStateMonths(Folder::TO_BE_REFUND);
        $foldersMonthsValideWreck = $filterListFolder->foldersByStateMonths(Folder::SELLING_STANDBY);
        return $this->render('insurance/sinister/skeleton_page/home.html.twig', ['folderByState' => $countFolder,
            'foldersMonthValid' => $foldersMonthsValide,
            'foldersMonthValidWreck' => $foldersMonthsValideWreck]);
    }

    /**
     * @Route("/list-folder", name="folder-sinister-validator")
     * @param FolderRepository $folderRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFolder(FolderRepository $folderRepository)
    {
        $normalFolders = $folderRepository->findBy(['state' => Folder::SUBMITTED]);
        $wreckFolders = $folderRepository->findBy(['state' => Folder::WRECK_REPORT_SENT]);
        return $this->render(
            'insurance/sinister/validator_teams/listFolderSinisterValidator.html.twig',
            ['normalFolders' => $normalFolders, 'wreckFolders' => $wreckFolders]
        );
    }

    /**
     * @param AttachedFile $attachedFile
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/changeAFState/{id}", name="changeAttachedFilState", options={"expose"=true})
     */
    public function changeAttachedFileState(AttachedFile $attachedFile, EntityManagerInterface $manager, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $attachedFile->setValidated(!$attachedFile->getValidated());
            $manager->flush();
            return $this->json('File status changed');
        }
        return $this->redirectToRoute('expert-home');
    }

    /**
     * @Route("/details-folder/{id}", name="folder-details")
     * @param Folder $folder
     * @param FolderService $folderService
     * @param AttachedFilesService $attachedFilesService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsFolder(Folder $folder, FolderService $folderService, AttachedFilesService $attachedFilesService)
    {
        if ($folder->getState() == Folder::SUBMITTED || $folder->getState() == Folder::WRECK_REPORT_SENT) {
            $expert = $folderService->getExpertInCharge($folder);
            $attachedFiles = $folder->getAttachedFile();
            $wreckReport=null;
            if ($folder->getState() == Folder::WRECK_REPORT_SENT) {
                $wreckReport = $folder->getWreckagereport();
            }
            $attachedFilesService->addImageBase64($folder);
            return $this->render(
                'insurance/sinister/validator_teams/detailsFolderSinister.html.twig',
                [
                    'expert' => $expert,
                    'folder' => $folder,
                    'attachedFiles' => $attachedFiles,
                    'wreckReportDetails' => $wreckReport]
            );
        } else {
            return $this->redirectToRoute('folder-sinister-validator');
        }
    }

    /**
     * @Route("/validate-folder/{id}", name="validate-folder")
     * @param Folder $folder
     * @param ObjectManager $manager
     * @param Session $session
     * @param ExpertiseService $expertiseService
     * @param FolderService $folderService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateFolder(
        Folder $folder,
        ObjectManager $manager,
        Session $session,
        ExpertiseService
        $expertiseService,
        FolderService $folderService
    ) {
        if ($folderService->changeFolderState($folder, 'validate_submission', null, $this->getUser())) {
            $expertises = $folder->getExpertises();
            foreach ($expertises as $expertise) {
                $expertiseService->setTotalExpertiseHonorary($expertise);
            }
            $manager->flush();
            $session->getFlashBag()->add('success', 'le dossier est validé avec succes');
            return $this->redirectToRoute('folder-sinister-validator');
        } elseif ($folderService->changeFolderState($folder, 'validate_wreck_report', null, $this->getUser())) {
            $session->getFlashBag()->add('success', 'le dossier épave est validé avec succes');
            return $this->redirectToRoute('folder-sinister-validator');
        } else {
            $session->getFlashBag()->add('error', 'le dossier n\'est pas pu etre validé :(');
            return $this->redirectToRoute('folder-details', ['id' => $folder->getId()]);
        }
    }

    /**
     * @Route("/reconsider-folder/", name="reconsider-folder")
     * @param Session $session
     * @param Request $request
     * @param FolderRepository $folderRepository
     * @param FolderService $folderService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reconsiderFolder(
        Session $session,
        Request $request,
        FolderRepository $folderRepository,
        FolderService $folderService
    ) {
        if ($request->isXmlHttpRequest()) {
            $idFolder = $_POST['idFolder'];
            $folder = $folderRepository->find($idFolder);
            $comments = $_POST['comments'];
            if ($folderService->changeFolderState($folder, 'to_reconsider', $comments, $this->getUser())) {
                $session->getFlashBag()->add('succes', 'le dossier ' . $folder->getRef() . ' a été refusé');
                return new JsonResponse('success');
            } else {
                $session->getFlashBag()->add('error', 'un erreur est survenu, le dossier ne peut pas etre retourné. Veuillez notifier l\'admin');
                return new JsonResponse('transitionError');
            }
        }
        return new JsonResponse('un erreur est survenu (x_x), essayez une autre fois ou veuillez notifier l\'admin');
    }

    /**
     * @Route("/reassign-folder", name="reassign-folder")
     * @param Session $session
     * @param Registry $workflows
     * @param Request $request
     * @param FolderRepository $folderRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reassignFolder(
        Session $session,
        Registry $workflows,
        Request $request,
        FolderRepository $folderRepository
    ) {
        if ($request->isXmlHttpRequest()) {
            $idFolder = $_POST['idFolder'];
            $folder = $folderRepository->find($idFolder);
            $comments = $_POST['comments'];
            $session->set('comments', $comments);
            $workflow = $workflows->get($folder);
            if ($workflow->can($folder, 'reassign')) {
                $session->getFlashBag()->add('succes', 'le dossier ' . $folder->getRef() . ' a été réaffecté');
                return new JsonResponse('success');
            } else {
                $session->getFlashBag()->add('error', 'un erreur est survenu, le dossier ne peut pas etre réaffecté. Veuillez notifier l\'admin');
                return new JsonResponse('transitionError');
            }
        }
        return new JsonResponse('un erreur est survenu (x_x), essayez une autre fois ou veuillez notifier l\'admin');
    }

    /**
     * @Route("/list-experts-to-reassign/{id}", name="list-experts-to-reassign")
     * @param Folder $folder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listExpertToReassign(Folder $folder)
    {
        return $this->render('insurance/sinister/validator_teams/reassignFolder.html.twig', ['folder' => $folder]);
    }

    /**
     * @Route("/chosen-new-expert", name="new-expert-reassigned",options={"expose"=true})
     * @param ExpertiseService $expertiseService
     * @param Session $session
     * @param Request $request
     * @param ExpertRepository $expertRepository
     * @param FolderRepository $folderRepository
     * @param FolderService $folderService
     * @return Mixed
     */
    public function reassignNewExpert(
        ExpertiseService $expertiseService,
        Session $session,
        Request $request,
        ExpertRepository $expertRepository,
        FolderRepository $folderRepository,
        FolderService $folderService
    ) {
        if ($request->isXmlHttpRequest()) {
            $idFolder = $_POST['idFolder'];
            $folder = $folderRepository->find($idFolder);
            $idExpert = $_POST['idExpert'];
            $comments = $session->get('comments');
            $session->clear();
            if ($idExpert) {
                $expert = $expertRepository->find($idExpert);
                if ($folderService->changeFolderState($folder, 'reassign', $comments, $this->getUser())) {
                    if ($expertiseService->createExpertise($folder, $expert)) {
                        $msg = 'success';
                        $session->getFlashBag()->add('success', 'le dossier est réaffecté avec succes');
                    }
                } else {
                    $msg = 'un erreur est survenu essayez une autre fois';
                }
            } else {
                $msg = 'choisissez un expert s\'il vous plait';
            }
            return new JsonResponse($msg);
        }
        return $this->redirectToRoute('folder-sinister-validator');
    }

    /**
     * @Route("/experts-choice/{id}", name="choice-expert")
     * @param Request $request
     * @param ExpertChoiceList $expertChoiceList
     * @param Folder $folder
     * @return JsonResponse
     */
    public function handleExpertChoice(Request $request, ExpertChoiceList $expertChoiceList, Folder $folder): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $experts = [];
            $expertises = $folder->getExpertises();
            foreach ($expertises as $expertise) {
                $experts[] = $expertise->getExpert()->getId();
            }
            $length = $request->get('length');
            $start = $request->get('start');
            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];
            $draw = $request->get('draw');
            $result = $expertChoiceList->expertToRessignList($experts, $filters['query'], $start, $length, $draw);
            $jsonResponse = new JsonResponse();
            return $jsonResponse->setData($result);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/handleFolder",name="handleFolder")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleFolder(Request $request, DataTablesInterface $datatables): JsonResponse
    {
        try {
            $results = $datatables->handle($request, 'validatorFolder');
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }
}
