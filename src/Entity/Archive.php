<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArchiveRepository")
 */
class Archive
{
    const BEFORE_REPAIR_UPLOAD_IMAGE_TYPE = 'Ajout Image';
    const AFTER_REPAIR_UPLOAD_IMAGE_TYPE = 'Ajout Image';
    const DEVIS_REPARATION_UPLOAD_TYPE = 'Ajout devis';
    const ADD_BILL_TYPE = 'Ajout facture';
    const DELETE_BILL_TYPE = 'Suppression facture';
    const ADD_CRASH_POINT_TYPE = 'Ajout point de choc';
    const RESET_CRASH_POINTS_TYPE = 'Suppression points de choc';
    const DELETE_ATTACHED_FILE_TYPE = 'Suppression pièces jointes';
    const TRANSITION_TYPE = 'Changement d\'état';
    const SELLING_REPORT_TYPE = 'Ajout contrat';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $action_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Folder", inversedBy="archives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $folder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bill", inversedBy="archives")
     */
    private $bill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WreckageReport", inversedBy="archives")
     */
    private $wreckageReport;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AttachedFile", inversedBy="archives")
     */
    private $attachedFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="archives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionDate(): ?\DateTimeInterface
    {
        return $this->action_date;
    }

    public function setActionDate(\DateTimeInterface $action_date): self
    {
        $this->action_date = $action_date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getBill(): ?Bill
    {
        return $this->bill;
    }

    public function setBill(?Bill $bill): self
    {
        $this->bill = $bill;

        return $this;
    }

    public function getWreckageReport(): ?WreckageReport
    {
        return $this->wreckageReport;
    }

    public function setWreckageReport(?WreckageReport $wreckageReport): self
    {
        $this->wreckageReport = $wreckageReport;

        return $this;
    }

    public function getAttachedFile(): ?AttachedFile
    {
        return $this->attachedFile;
    }

    public function setAttachedFile(?AttachedFile $attachedFile): self
    {
        $this->attachedFile = $attachedFile;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
