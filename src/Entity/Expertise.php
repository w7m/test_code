<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpertiseRepository")
 */
class Expertise
{
    //refund status contants
    const WAITING = 'waiting-validation';
    const VALIDATED = 'validated';
    const REFUNDED = 'refunded';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $assignmentDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $expertiseLevel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $modificationAbility;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $honorary;
    /**
     * @ORM\Column(type="array",nullable=true)
     */
    private $honoraryParameters;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $refundStatus;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $paymentDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $paymentAmount;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $checkNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Expert", inversedBy="expertises")
     */
    private $expert;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Paiment", inversedBy="expertises")
     * @ORM\JoinColumn(nullable=true)
     */
    private $paiment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Folder", inversedBy="expertises")
     * @ORM\JoinColumn(nullable=false)
     */
    private $folder;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getAssignmentDate(): ?\DateTimeInterface
    {
        return $this->assignmentDate;
    }

    /**
     * @param \DateTimeInterface $assignmentDate
     * @return Expertise
     */
    public function setAssignmentDate(\DateTimeInterface $assignmentDate): self
    {
        $this->assignmentDate = $assignmentDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpertiseLevel(): ?int
    {
        return $this->expertiseLevel;
    }

    /**
     * @param int $expertiseLevel
     * @return Expertise
     */
    public function setExpertiseLevel(int $expertiseLevel): self
    {
        $this->expertiseLevel = $expertiseLevel;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getModificationAbility(): ?bool
    {
        return $this->modificationAbility;
    }

    /**
     * @param bool $modificationAbility
     * @return Expertise
     */
    public function setModificationAbility(bool $modificationAbility): self
    {
        $this->modificationAbility = $modificationAbility;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHonorary()
    {
        return $this->honorary;
    }

    /**
     * @param mixed $honorary
     * @return Expertise
     */
    public function setHonorary($honorary): self
    {
        $this->honorary = $honorary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHonoraryParameters()
    {
        return $this->honoraryParameters;
    }

    /**
     * @param mixed $honoraryParameters
     */
    public function setHonoraryParameters($honoraryParameters): void
    {
        $this->honoraryParameters = $honoraryParameters;
    }

    public function getRefundStatus(): ?string
    {
        return $this->refundStatus;
    }

    public function setRefundStatus(string $refundStatus): self
    {
        $this->refundStatus = $refundStatus;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentAmount(): ?int
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(int $paymentAmount): self
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    public function getCheckNumber(): ?int
    {
        return $this->checkNumber;
    }

    public function setCheckNumber(int $checkNumber): self
    {
        $this->checkNumber = $checkNumber;

        return $this;
    }

    public function getExpert(): ?Expert
    {
        return $this->expert;
    }

    public function setExpert(?Expert $expert): self
    {
        $this->expert = $expert;

        return $this;
    }

    public function getPaiment(): ?Paiment
    {
        return $this->paiment;
    }

    public function setPaiment(?Paiment $paiment): self
    {
        $this->paiment = $paiment;

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
}
