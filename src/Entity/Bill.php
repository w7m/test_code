<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillRepository")
 */
class Bill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $setDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bill_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bill_ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $works;

    /**
     * @ORM\Column(type="float")
     */
    private $realAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $estimaedAmount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Folder", inversedBy="bills")
     * @ORM\JoinColumn(nullable=true)
     */
    private $folder;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Archive", mappedBy="bill")
     */
    private $archives;

    public function __construct()
    {
        $this->archives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetDate(): ?\DateTimeInterface
    {
        return $this->setDate;
    }

    public function setSetDate(\DateTimeInterface $setDate): self
    {
        $this->setDate = $setDate;

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

    public function getBillDate(): ?\DateTimeInterface
    {
        return $this->bill_date;
    }

    public function setBillDate(\DateTimeInterface $bill_date): self
    {
        $this->bill_date = $bill_date;

        return $this;
    }

    public function getBillRef(): ?string
    {
        return $this->bill_ref;
    }

    public function setBillRef(string $bill_ref): self
    {
        $this->bill_ref = $bill_ref;

        return $this;
    }

    public function getWorks(): ?string
    {
        return $this->works;
    }

    public function setWorks(string $works): self
    {
        $this->works = $works;

        return $this;
    }

    public function getRealAmount(): ?float
    {
        return $this->realAmount;
    }

    public function setRealAmount(float $realAmount): self
    {
        $this->realAmount = $realAmount;

        return $this;
    }

    public function getEstimaedAmount(): ?float
    {
        return $this->estimaedAmount;
    }

    public function setEstimaedAmount(float $estimaedAmount): self
    {
        $this->estimaedAmount = $estimaedAmount;

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
            $archive->setBill($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->contains($archive)) {
            $this->archives->removeElement($archive);
            // set the owning side to null (unless already changed)
            if ($archive->getBill() === $this) {
                $archive->setBill(null);
            }
        }

        return $this;
    }
}
