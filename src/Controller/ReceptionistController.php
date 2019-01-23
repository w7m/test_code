<?php

namespace App\Controller;

use App\Entity\AttachedFile;
use App\Entity\Ensured;
use App\Entity\Expert;
use App\Entity\Folder;
use App\Entity\Vehicle;
use App\Form\AttachedFileType;
use App\Form\EnsuredType;
use App\Repository\ExpertRepository;
use App\Repository\FolderRepository;
use App\Repository\VehicleRepository;
use App\Service\FilterListFolder;
use App\Service\NewFolder;
use App\Service\PDFGenerator;
use App\Service\VehicleService;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/receptionist", name="")
 */
class ReceptionistController extends AbstractController
{
    /**
     * @Route("/", name="home-receptionist")
     *
     * @param FolderRepository $folderRepository
     * @param VehicleRepository $vehicleRepository
     * @param VehicleService $vehicleService
     * @param FilterListFolder $filterListFolder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(FolderRepository $folderRepository, VehicleRepository $vehicleRepository,VehicleService $vehicleService,FilterListFolder $filterListFolder)
    {
        $numbersContract = $vehicleRepository->totalVehicles();

        $numbersFolders = $folderRepository->totlaFoldes();

        $vehicleByMonths = $vehicleService->vehicleByeMonths();

        $listFolder = $filterListFolder->foldersByStateMonths();
        return $this->render('insurance/sinister/skeleton_page/home.html.twig', ['numbersFolders' => $numbersFolders,
            'numbersContract'=>$numbersContract,
            'vehicleByMonths'=>$vehicleByMonths,
            'listFolder'=>$listFolder]);
    }

    /**
     * @Route("/contracts", name="all-contract")
     *
     * @param VehicleRepository $vehicleRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listContract(VehicleRepository $vehicleRepository)
    {
        return $this->render('insurance/sinister/receptionist/listContract.html.twig', [
            'all_contract' => $vehicleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new-contract", name="new-contract", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newContract(Request $request): Response
    {
        $ensured = new Ensured();
        $form = $this->createForm(EnsuredType::class, $ensured);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $vehicles = $ensured->getVehicles();
            foreach ($vehicles as $vehicle) {
                $vehicle->setEnsured($ensured);
                $entityManager->persist($vehicle);
            }
            try {
                $entityManager->persist($ensured);
                $entityManager->flush();
                $request->getSession()->getFlashBag()->add('success', 'un contrat ajouté avec succès');

                return $this->redirectToRoute('all-contract');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est s\'est produite l\'or de l\'ajout , veuillez ressayer de nouveau');
            }
        }

        return $this->render('insurance/sinister/receptionist/newContrat.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/folder-sinister", name="all-folder-sinister")
     * @param FolderRepository $folderRepository
     * @return Response
     */
    public function listFolderSinister(FolderRepository $folderRepository)
    {
        $folders = $folderRepository->findBy(['state' => Folder::CREATED]);

        return $this->render('insurance/sinister/receptionist/listFolderSinister.html.twig',
            ['folders' => $folders]
        );
    }

    /**
     * @Route("/new-sinister", name="new-folder-sinister", methods={"GET","POST"})
     *
     * @param ExpertRepository $expertRepository
     * @param VehicleRepository $vehicleRepository
     * @param Request $request
     *
     * @return Response
     */
    public function newFolderSinister(ExpertRepository $expertRepository, VehicleRepository $vehicleRepository, Request $request): Response
    {
        $image = new AttachedFile();
        $imageForm = $this->createForm(AttachedFileType::class, $image);
        if ($request->isXmlHttpRequest()) {
            $val = $request->get('val');
            $valExp = $request->get('valExp');
            $arr = $request->get('idArr');
            $data = $vehicleRepository->findOneByInsured($val);
            $dataExp = $expertRepository->findByExpert($valExp);
            return new JsonResponse([$dataExp, $data, $arr]);


        }

        return $this->render('insurance/sinister/receptionist/newFolderSinister.html.twig', [
            'imageForm' => $imageForm->createView()
        ]);
    }

    /**
     * @Route("/newfolder", name="folder_new", methods={"GET","POST"})
     * @param ExpertRepository $expertRepository
     * @param NewFolder $newFolder
     * @param VehicleRepository $vehicleRepository
     * @param Request $request
     * @return Response
     */
    public function new(ExpertRepository $expertRepository, NewFolder $newFolder, VehicleRepository $vehicleRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $constatform = $request->get('constatform');
            $vehicleId = $request->get('id_veh');
            $expertId = $request->get('id_exp');

            $img_file = explode('\\', $constatform)[2];
            $expert = $expertRepository->find($expertId);
            $vehicle = $vehicleRepository->find($vehicleId);
            if (!$expert || !$vehicle) {
                return $this->json('error');
            } else {

                $image = new AttachedFile();
                $imageForm = $this->createForm(AttachedFileType::class, $image);
                $imageForm->handleRequest($request);
                $newFolder->newFolder($image, $img_file, $expert, $vehicle);

                return $this->json('success');
            }
        }

    }
    /**
     * @Route("/generatePdf/{id}/{expert}",name="generatePdf")
     * @param HttpKernelInterface $kernel
     * @param Vehicle $vehicle
     * @param Expert $expert
     * @param PDFGenerator $generator
     * @return Response
     */

    public function pdf(HttpKernelInterface $kernel, Vehicle $vehicle, Expert $expert, PDFGenerator $generator)
    {
        $generator->generatePdf($vehicle, $expert, $kernel);
        // Send some text response
      //  return new Response("L'ordre de mission a été généré avec succès !");
    }

    /**
     * @Route("/sinister/{id}", name="sinister_show", methods={"GET"})
     *
     * @param Folder $folder
     *
     * @return Response
     */
    public function show(Folder $folder)
    {
        return $this->render('insurance/sinister/receptionist/detailFolderSinister.html.twig', ['folder' => $folder]);
    }

    /**
     * @Route("/experts-choice-receptionist", name="choice-expert-receptionist")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleExpertChoice(Request $request, DataTablesInterface $datatables): JsonResponse
    {
        try {
            $results = $datatables->handle($request, 'experts_choice');
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/handleReceptionistFolder",name="handleReceptionistFolder")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleFolder(Request $request, DataTablesInterface $datatables): JsonResponse
    {
        try {
            $results = $datatables->handle($request, 'folder');
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/handleReceptionistContract", name="handleReceptionistContract")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleContract(Request $request, DataTablesInterface $datatables): JsonResponse
    {
        try {

            $results = $datatables->handle($request, 'contract');

            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/folderDetails/{id}",name="RecepfolderDetails")
     * @param Folder $folder
     * @return Response
     */
    public function folderDetails(Folder $folder)
    {

        if (!empty($folder->getExpertises()->toArray())) {
            $expert = $folder->getExpertises()->toArray()[0]->getExpert();
            $vehicle = $folder->getVehicle();
            $ensured = $vehicle->getEnsured();
            return $this->render('insurance/sinister/receptionist/detailFolderSinister.html.twig', ['expert' => $expert, 'folder' => $folder, 'vehicle' => $vehicle, 'ensured' => $ensured]);

        } else {
            return $this->render('insurance/sinister/receptionist/folderIntrouvable.html.twig');

        }


    }

    /**
     * @Route("/contractDetails/{id}", name="RecepContractDetails")
     * @param Vehicle $vehicle
     * @return Response
     */
    public function contractDetails(Vehicle $vehicle)
    {
        return $this->render('insurance/sinister/receptionist/detailsContract.html.twig', ['vehicule' => $vehicle]);
    }
}
