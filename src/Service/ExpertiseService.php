<?php
/**
 * Created by PhpStorm.
 * User: ebensaid
 * Date: 09/01/2019
 * Time: 12:16
 */

namespace App\Service;


use App\Entity\Expert;
use App\Entity\Expertise;
use App\Entity\Folder;
use App\Repository\ExpertiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ExpertiseService
{
    private $entityManager;
    private $expertiseRepository;
    private $folderService;
    private $billsService;
    private $request;

    /**
     * FolderService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ExpertiseRepository $expertiseRepository
     * @param FolderService $folderService
     * @param BillsService $billsService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExpertiseRepository $expertiseRepository,
        FolderService $folderService,
        BillsService $billsService
    )
    {
        $this->entityManager = $entityManager;
        $this->expertiseRepository = $expertiseRepository;
        $this->folderService = $folderService;
        $this->billsService = $billsService;
        $this->request = new Request();
    }

    /**
     * @param Folder $folder
     * @param Expert $expert
     * @return bool
     */
    public function createExpertise(Folder $folder, Expert $expert)
    {
        $lastExpertise = $this->expertiseRepository->findOneBy(['folder'=>$folder], ['assignmentDate'=>'DESC']);
        $lastExpertise->setModificationAbility(0);
        $level = $lastExpertise->getExpertiseLevel()+1;
        $expertise= new Expertise();
        $expertise->setExpert($expert);
        $expertise->setFolder($folder);
        $expertise->setModificationAbility(1);
        $expertise->setAssignmentDate(new \DateTime());
        $expertise->setExpertiseLevel($level);
        $expertise->setRefundStatus(Expertise::WAITING);
        $this->entityManager->persist($expertise);
        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            $this->request->getSession()->getFlashBag()->add('error', 'Une erreur est s\'est produite l\'or de la création de l\'expertise , veuillez ressayer de nouveau');
            return false;
        }
    }

    /**
     * @param Expertise $expertise
     */
    public function setTotalExpertiseHonorary(Expertise $expertise): void
    {
        $parameters = $expertise->getHonoraryParameters();
        $imgNumber= $this->folderService->getNumberValidatedImage($expertise->getFolder());
        $total= $imgNumber *$parameters['photoPrice']+$parameters['openingFileExpense']+$this->billsService->getTotalAmountBills($expertise->getFolder())*$parameters['billPercentage']/100;
        $expertise->setHonorary($total);
        $this->entityManager->flush();
    }
}