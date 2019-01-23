<?php
/**
 * Created by PhpStorm.
 * User: Mohamed Amine Lejmi
 * Date: 13/01/2019
 * Time: 17:48
 */

namespace App\Service;


use App\Entity\Archive;
use App\Entity\Folder;
use App\Entity\User;
use App\Repository\ArchiveRepository;
use App\Repository\AttachedFileRepository;
use Doctrine\ORM\EntityManagerInterface;

class HistoryService
{

    private $manager;
    private $attachedFileRepo;

    public function __construct(EntityManagerInterface $manager, AttachedFileRepository $attachedFileRepository)
    {
        $this->manager = $manager;
        $this->attachedFileRepo = $attachedFileRepository;
    }

    public function logToHistory(User $user, Folder $folder, $type, array $options = null)
    {
        $archive = new Archive();
        $archive->setUser($user);
        $archive->setFolder($folder);
        $archive->setActionDate(new \DateTime('now'));
        switch ($type) {
            case 'beforeRepairImage':
                $archive->setAttachedFile($options['attached_file']);
                $archive->setType(Archive::BEFORE_REPAIR_UPLOAD_IMAGE_TYPE);
                $archive->setAction('Ajout d\'ìmage avant réparation');
                break;
            case 'afterRepairImage':
                $archive->setAttachedFile($options['attached_file']);
                $archive->setType(Archive::AFTER_REPAIR_UPLOAD_IMAGE_TYPE);
                $archive->setAction('Ajout d\'ìmage après réparation');
                break;
            case 'devisRepair':
                $archive->setAttachedFile($options['attached_file']);
                $archive->setType(Archive::DEVIS_REPARATION_UPLOAD_TYPE);
                $archive->setAction('Ajout d\'un devis de réparation');
                break;
            case 'addBill':
                $archive->setBill($options['bill']);
                $archive->setType(Archive::ADD_BILL_TYPE);
                $archive->setAction('Ajout d\'une nouvelle facture');
                break;
            case 'addCrashPoint':
                $archive->setType(Archive::ADD_CRASH_POINT_TYPE);
                $archive->setAction('Ajout d\'un nouveau point de choc');
                break;
            case 'deleteBill':
                $archive->setBill($options['bill']);
                $archive->setType(Archive::DELETE_BILL_TYPE);
                $archive->setAction('Suppression d\'une facture');
                break;
            case 'deleteCrashPoints':
                $archive->setType(Archive::RESET_CRASH_POINTS_TYPE);
                $archive->setAction('Suppression des points de choc');
                break;
            case 'deleteAttachedFile':
                $archive->setAttachedFile($options['attached_file']);
                $archive->setType(Archive::DELETE_ATTACHED_FILE_TYPE);
                $archive->setAction('Suppression d\'une pièce jointe');
                break;
            case 'sellingReport':
                $archive->setAttachedFile($options['attached_file']);
                $archive->setType(Archive::SELLING_REPORT_TYPE);
                $archive->setAction('Ajout d\'un contrat de vente épave');
                break;

        }
        $this->manager->persist($archive);
        $this->manager->flush();
    }

    public function logTransitionToHistory(User $user, Folder $folder, $transition, $comments = null)
    {
        $archive = new Archive();
        $archive->setUser($user);
        $archive->setFolder($folder);
        $archive->setActionDate(new \DateTime('now'));
        $archive->setType(Archive::TRANSITION_TYPE);
        if ($comments != null) {
            $archive->setComments($comments);
        }
        switch ($transition) {
            case 'treat':
                $archive->setAction('Le dossier est en cours de traitement');
                break;
            case 'reassign':
                $archive->setAction('Le dossier est réattribué');
                break;
            case 'to_reconsider':
                $archive->setAction('Le dossier est en contre expertise');
                break;
            case 'validate_submission':
                $archive->setAction('Le dossier est en validation financière');
                break;
            case 'validate_wreck_report':
                $archive->setAction('Le dossier est validé en tant qu\'épave');
                break;
            case 'write_sales_contract':
                $archive->setAction('Le dossier est en validation financière');
                break;
            case 'write_wreck_report':
                $archive->setAction('Le dossier est soumis en tant qu\'épave');
                break;
            case 'submit_folder':
                $archive->setAction('Le dossier est soumis en tant que réparation');
                break;
            case 'validate_refund':
                $archive->setAction('La peiement du dossier est validé');
                break;
        }

        $this->manager->persist($archive);
        $this->manager->flush();
    }
}