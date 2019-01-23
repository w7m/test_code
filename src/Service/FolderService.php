<?php
/**
 * Created by PhpStorm.
 * User: ebensaid
 * Date: 07/01/2019
 * Time: 10:36
 */

namespace App\Service;

use App\Entity\Expert;
use App\Entity\Expertise;
use App\Entity\Folder;
use App\Entity\WreckageReport;
use App\Repository\AttachedFileRepository;
use App\Repository\ExpertiseRepository;
use App\Repository\WreckageReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

class FolderService
{
    private $entityManager;
    private $expertiseRepository;
    private $wrRepository;
    private $workflows;
    private $attachedFileRepository;
    private $historyService;


    /**
     * FolderService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ExpertiseRepository $expertiseRepository
     * @param WreckageReportRepository $wrRepository
     * @param Registry $workflows
     * @param AttachedFileRepository $attachedFileRepository
     * @param HistoryService $historyService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExpertiseRepository $expertiseRepository,
        WreckageReportRepository $wrRepository,
        Registry $workflows,
        AttachedFileRepository $attachedFileRepository,
        HistoryService $historyService
    ) {
        $this->entityManager = $entityManager;
        $this->expertiseRepository = $expertiseRepository;
        $this->wrRepository = $wrRepository;
        $this->workflows = $workflows;
        $this->attachedFileRepository = $attachedFileRepository;
        $this->historyService = $historyService;
    }

    /**
     * @param Folder $folder
     * @return Expert
     */
    public function getExpertInCharge(Folder $folder): Expert
    {
        return $this->expertiseRepository->findOneBy(['folder' => $folder], ['assignmentDate' => 'DESC'])->getExpert();
    }

    /**
     * @param Folder $folder
     * @return float|int|null
     */
    public function changeFolderState($folder, $transition, $comments =null, $user = null)
    {
        $workflow = $this->workflows->get($folder);
        if ($workflow->can($folder, $transition)) {
            $workflow->apply($folder, $transition);
            $this->entityManager->flush();
            $this->historyService->logTransitionToHistory($user,$folder,$transition,$comments);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Folder $folder
     * @return float|int|null
     */
    public function billsTotalAmount(Folder $folder)
    {
        $totalAmount = 0;
        $bills = $folder->getBills();
        foreach ($bills as $bill) {
            $totalAmount += $bill->getRealAmount();
        }
        return $totalAmount;
    }

    /**
     * @param Folder $folder
     */
    public function validateEnsuredRefund(Folder $folder)
    {
        $folder->setPaymentDate(new \DateTime());
        $folder->setRefund(1);
        $folder->setPaymentAmount($this->billsTotalAmount(($folder)));
        $this->entityManager->flush();
    }

    /**
     * @param Folder $folder
     */
    public function validateExpertsRefund(Folder $folder)
    {
        $expertises = $folder->getExpertises();
        foreach ($expertises as $expertise) {
            $expertise->setRefundStatus(Expertise::VALIDATED);
        }
        $this->entityManager->flush();
    }

    /**
     * @param $folder
     * @return int
     */
    public function getNumberValidatedImage($folder)
    {
        $imagesValidated= $this->attachedFileRepository->findBy(['folder'=>$folder,'validated'=>1]);
        if ($imagesValidated == null){
            return 0;
        } else {
            return count($imagesValidated);
        }
    }
}
