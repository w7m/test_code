<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FolderRepository")
 */
class Folder
{
    const WRECK_REPORT_SENT = 'wreck-report-sent';
    const TO_BE_REFUND = 'to-be-refunded';
    const SUBMITTED = 'submitted';
    const CREATED = 'created';
    const TO_BE_RECONSEDERED = 'to-be-reconsidered';
    const REASSIGNED = 'reassigned';
    const IN_PROGRESS = 'in-progress';
    const SELLING_STANDBY = 'selling-standby';
    const CLOSED = 'closed';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50,unique=true)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     */
    private $refund;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $payment_amount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $check_number;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $payment_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Expertise", mappedBy="folder", orphanRemoval=true)
     */
    private $expertises;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle", inversedBy="folders")
     */
    private $vehicle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bill", mappedBy="folder", orphanRemoval=true)
     */
    private $bills;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\WreckageReport")
     */
    private $wreckagereport;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttachedFile", mappedBy="folder", orphanRemoval=true)
     */
    private $attachedFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Archive", mappedBy="folder", orphanRemoval=true)
     */
    private $archives;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $crashPoints;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWreck;



    public function __construct()
    {
        $this->expertises = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->attachedFile = new ArrayCollection();
        $this->archives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getRefund(): ?string
    {
        return $this->refund;
    }

    public function setRefund(string $refund): self
    {
        $this->refund = $refund;

        return $this;
    }

    public function getPaymentAmount(): ?int
    {
        return $this->payment_amount;
    }

    public function setPaymentAmount(int $payment_amount): self
    {
        $this->payment_amount = $payment_amount;

        return $this;
    }

    public function getCheckNumber(): ?int
    {
        return $this->check_number;
    }

    public function setCheckNumber(int $check_number): self
    {
        $this->check_number = $check_number;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeInterface $payment_date): self
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    /**
     * @return Collection|Expertise[]
     */
    public function getExpertises(): Collection
    {
        return $this->expertises;
    }

    public function addExpertise(Expertise $expertise): self
    {
        if (!$this->expertises->contains($expertise)) {
            $this->expertises[] = $expertise;
            $expertise->setFolder($this);
        }

        return $this;
    }

    public function removeExpertise(Expertise $expertise): self
    {
        if ($this->expertises->contains($expertise)) {
            $this->expertises->removeElement($expertise);
            // set the owning side to null (unless already changed)
            if ($expertise->getFolder() === $this) {
                $expertise->setFolder(null);
            }
        }

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return Collection|Bill[]
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $bill): self
    {
        if (!$this->bills->contains($bill)) {
            $this->bills[] = $bill;
            $bill->setFolder($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getFolder() === $this) {
                $bill->setFolder(null);
            }
        }

        return $this;
    }

    
    /**
     * @return Collection|AttachedFile[]
     */
    public function getAttachedFile(): Collection
    {
        return $this->attachedFile;
    }

    public function addAttachedFile(AttachedFile $attachedFile): self
    {
        if (!$this->attachedFile->contains($attachedFile)) {
            $this->attachedFile[] = $attachedFile;
            $attachedFile->setFolder($this);
        }

        return $this;
    }

    public function removeAttachedFile(AttachedFile $attachedFile): self
    {
        if ($this->attachedFile->contains($attachedFile)) {
            $this->attachedFile->removeElement($attachedFile);
            // set the owning side to null (unless already changed)
            if ($attachedFile->getFolder() === $this) {
                $attachedFile->setFolder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Archive[]
     */
    public function getArchives(): Collection
    {
        return $this->archives;
    }

    public function addArchive(Archive $archive): self
    {
        if (!$this->archives->contains($archive)) {
            $this->archives[] = $archive;
            $archive->setFolder($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->contains($archive)) {
            $this->archives->removeElement($archive);
            // set the owning side to null (unless already changed)
            if ($archive->getFolder() === $this) {
                $archive->setFolder(null);
            }
        }

        return $this;
    }

    public function getCrashPoints(): ?array
    {
        return $this->crashPoints;
    }

    public function setCrashPoints(?array $crashPoints): self
    {
        $this->crashPoints = $crashPoints;

        return $this;
    }

    public function addCrashPoint($point)
    {
        $crashPoints = $this->getCrashPoints();
        if ($crashPoints == null) {
            $crashPoints = [];
        }
        array_push($crashPoints, $point);
        $this->setCrashPoints($crashPoints);
        return $this;
    }

    public function getisWreck(): ?bool
    {
        return $this->isWreck;
    }

    public function setisWreck(?bool $isWreck): self
    {
        $this->isWreck = $isWreck;

        return $this;
    }

    public function getWreckagereport(): ?WreckageReport
    {
        return $this->wreckagereport;
    }

    public function setWreckagereport(?WreckageReport $wreckagereport): self
    {
        $this->wreckagereport = $wreckagereport;

        return $this;
    }

}
