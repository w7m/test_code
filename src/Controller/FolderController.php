<?php
/**
 * Created by PhpStorm.
 * User: malejmi
 * Date: 07/01/2019
 * Time: 09:55.
 */

namespace App\Controller;

use App\Entity\AttachedFile;
use App\Entity\Bill;
use App\Entity\Folder;
use App\Entity\WreckageReport;
use App\Form\BillType;
use App\Form\RapportEpaveType;
use App\Repository\ExpertiseRepository;
use App\Repository\FolderRepository;
use App\Service\AttachedFilesService;
use App\Service\FolderService;
use App\Service\HistoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FolderController
 * @package App\Controller
 * @Route("/expert/folder")
 */
class FolderController extends AbstractController
{
    /**
     * @param Request $request
     * @param FolderService $folderService
     * @param AttachedFilesService $attachedFilesService
     * @param ExpertiseRepository $expertiseRepository
     * @param Folder $folder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/edit/{id}", name="editFolder")
     */
    public function editFolder(
        Request $request,
        FolderService $folderService,
        AttachedFilesService $attachedFilesService,
        ExpertiseRepository $expertiseRepository,
        Folder $folder = null
    ) {
        $thisExpertise = $expertiseRepository->findOneBy([
            'folder' => $folder,
            'expert' => $this->getUser()
        ]);
        if ($folder->getState() == Folder::SUBMITTED) {
            return $this->redirectToRoute('expert-home');
        }
        if ($folder->getState() === Folder::CREATED || $folder->getState() === Folder::TO_BE_RECONSEDERED || $folder->getState() === Folder::REASSIGNED) {
            $folderService->changeFolderState($folder, 'treat', null, $this->getUser());
        }
        if ($folder == null) {
            return new Response('Le dossier n\'existe pas !');
        }
        $attachedFilesService->addImageBase64($folder);
        $wreckageReport = $folder->getWreckagereport();
        if ($wreckageReport == null) {
            $wreckageReport = new WreckageReport();
        }
        $bill = new Bill();

        $billForm = $this->createForm(BillType::class, $bill);
        $billForm->handleRequest($request);

        $wreckageReportForm = $this->createForm(RapportEpaveType::class, $wreckageReport);
        $wreckageReportForm->handleRequest($request);

        if (!$thisExpertise->getModificationAbility()) {
            return $this->render('insurance/sinister/validator_teams/detailsFolderSinister.html.twig', [
                'folder' => $folder,
                'wreckReportDetails' => $folder->getWreckagereport()
            ]);
        }
        return $this->render('folder/editFolder.html.twig', [
            'folder' => $folder,
            'billForm' => $billForm->createView(),
            'epaveForm' => $wreckageReportForm->createView()
        ]);
    }

    /**
     * @param $idFolder
     * @param $imageType
     * @param FolderRepository $folderRepo
     * @param Request $request
     * @param AttachedFilesService $attachedFilesService
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/addImage/{imageType}/{idFolder}", name="addImage")
     */
    public function addAttachedFiles(
        $idFolder,
        $imageType,
        FolderRepository $folderRepo,
        Request $request,
        AttachedFilesService $attachedFilesService,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $folder = $folderRepo->find($idFolder);
            $file = $request->files->get('file');
            $output = $attachedFilesService->uploadImage($folder, $file, $imageType);
            $historyService->logToHistory($this->getUser(), $folder, $imageType, [
                'attached_file' => $output
            ]);
            return $this->json('File added');
        }
        return $this->redirectToRoute('expert-home');
    }


    /**
     * @param AttachedFile $attachedFile
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/deleteImage/{id}", name="deleteImage")
     */
    public function deleteAttachedFiles(
        AttachedFile $attachedFile,
        Request $request,
        EntityManagerInterface $manager,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $folder = $attachedFile->getFolder();
            $attachedFile->setFolder(null);
            $manager->flush();
            $historyService->logToHistory($this->getUser(), $folder, 'deleteAttachedFile', [
                'attached_file' => $attachedFile
            ]);
            return $this->json('File deleted');
        }
        return $this->redirectToRoute('expert-home');
    }


    /**
     * @Route("/addBill/{id}", name="addBill")
     * @param Folder $folder
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addBill(
        Folder $folder,
        Request $request,
        EntityManagerInterface $manager,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $bill = new Bill();
            $billForm = $this->createForm(BillType::class, $bill);
            $billForm->handleRequest($request);
            if ($billForm->isSubmitted() && $billForm->isValid()) {
                $bill->setSetDate(new \DateTime('now'));
                $bill->setFolder($folder);
                $folder->addBill($bill);
                $manager->persist($bill);
                $manager->flush();
                $output = [$bill->getId(), $bill->getBillRef(), $bill->getBillDate(), $bill->getType(),
                    $bill->getWorks(), $bill->getRealAmount(), $bill->getEstimaedAmount()];
                $historyService->logToHistory($this->getUser(), $folder, 'addBill', [
                    'bill' => $bill
                ]);
            }
            return $this->json($output);
        }
        return $this->redirectToRoute('expert-home');
    }

    /**
     * @param Bill $bill
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/deleteBill/{id}", name="deleteBill", options={"expose"=true})
     */
    public function deleteBill(
        Bill $bill,
        Request $request,
        EntityManagerInterface $manager,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $folder = $bill->getFolder();
            $bill->setFolder(null);
            $manager->flush();
            $historyService->logToHistory($this->getUser(), $folder, 'deleteBill', [
                'bill' => $bill
            ]);
            return $this->json('Bill deleted');
        }
        return $this->redirectToRoute('expert-home');
    }

    /**
     * @Route("/addCrashPoint/{id}", name="addCrashPoint", options={"expose"=true})
     * @param Folder $folder
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addCrashPoint(
        Folder $folder,
        Request $request,
        EntityManagerInterface $manager,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $crashPoint = $request->get('crashPoint');
            $folder->addCrashPoint($crashPoint);
            $manager->flush();
            $historyService->logToHistory($this->getUser(), $folder, 'addCrashPoint');
            return $this->json("Crash point has been added");
        }
        return $this->redirectToRoute('expert-home');
    }

    /**
     * @Route("/deleteCrashPoints/{id}", name="deleteCrashPoint", options={"expose"=true})
     * @param Folder $folder
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param HistoryService $historyService
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCrashPoint(
        Folder $folder,
        Request $request,
        EntityManagerInterface $manager,
        HistoryService $historyService
    ) {
        if ($request->isXmlHttpRequest()) {
            $folder->setCrashPoints(null);
            $manager->flush();
            $historyService->logToHistory($this->getUser(), $folder, 'deleteCrashPoints');
            return $this->json("Crash points has been cleared");
        }
        return $this->redirectToRoute('expert-home');
    }

    /**
     * @param Folder $folder
     * @param FolderService $folderService
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/submit/{id}", name="submitFolder")
     */
    public function submitFolder(Folder $folder, FolderService $folderService, Session $session)
    {
        $result = $folderService->changeFolderState($folder, 'submit_folder', null, $this->getUser());
        if ($result) {
            $session->getFlashBag()->add('success', 'Le dossier a été soumis');
            return $this->redirectToRoute('expert-Folders', ['state' => Folder::CREATED]);
        } else {
            $session->getFlashBag()->add('error', 'Le dossier n\'a pas pu être soumis');
            return $this->redirectToRoute('editFolder', ['id' => $folder->getId()]);
        }
    }

    /**
     * @param Folder $folder
     * @param FolderService $folderService
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/submitWreck/{id}", name="submitWreckFolder")
     */
    public function submitWreckFolder(Folder $folder, FolderService $folderService, Session $session)
    {
        $result = $folderService->changeFolderState($folder, 'write_wreck_report', null, $this->getUser());

        if ($result) {
            $session->getFlashBag()->add('success', 'Le dossier a été soumis');
            return $this->redirectToRoute('expert-Folders', ['state' => Folder::CREATED]);
        } else {
            $result = $folderService->changeFolderState($folder, 'write_sales_contract', null, $this->getUser());
            if ($result) {
                $session->getFlashBag()->add('success', 'Le dossier a été soumis');
                return $this->redirectToRoute('expert-Folders', ['state' => Folder::CREATED]);
            } else {
                $session->getFlashBag()->add('error', 'Le dossier n\'a pas pu être soumis');
                return $this->redirectToRoute('editFolder', ['id' => $folder->getId()]);
            }
        }
    }

    /**
     * @param Folder $folder
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("changeIsRepairState/{id}", name="changeIsRepairState", options={"expose"=true})
     */
    public function changeIsRepairState(Folder $folder, Request $request, EntityManagerInterface $manager)
    {
        if ($request->isXmlHttpRequest()) {
            $state = $folder->getisWreck();
            if ($state == null) {
                $state = false;
            }
            $folder->setisWreck(!$state);
            $manager->flush();
            return $this->json('Folder set to epave state');
        }
        return $this->json('Error', 500);
    }

    /**
     * @Route("/saveWreckageReport/{id}", name="saveWreckageReport")
     * @param Folder $folder
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveWreckageReport(Folder $folder, Request $request, EntityManagerInterface $manager)
    {
        if ($request->isXmlHttpRequest()) {
            $wreckageReport = $folder->getWreckagereport();
            if ($wreckageReport == null) {
                $wreckageReport = new WreckageReport();
            }
            $wreckageReportForm = $this->createForm(RapportEpaveType::class, $wreckageReport);
            $wreckageReportForm->handleRequest($request);
            if ($wreckageReportForm->isSubmitted() && $wreckageReportForm->isValid()) {
                $wreckageReport->setFolder($folder);
                $folder->setWreckagereport($wreckageReport);
                $wreckageReport->setReportDate(new \DateTime('now'));
                $manager->persist($wreckageReport);
                $manager->flush();
            }
            return $this->json('Wreckage report updated');
        }
        return $this->redirectToRoute('expert-home');
    }
}
