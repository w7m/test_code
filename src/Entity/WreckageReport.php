<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WreckageReportRepository")
 */
class WreckageReport
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
    private $reportDate;

    /**
     * @ORM\Column(type="float")
     */
    private $estimatedCarPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $repairAmount;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sellingPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Folder")
     * @ORM\JoinColumn(nullable=false)
     */
    private $folder;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Archive", mappedBy="wreckageReport")
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

    /**
     * @return mixed
     */
    public function getReportDate()
    {
        return $this->reportDate;
    }

    /**
     * @param mixed $reportDate
     */
    public function setReportDate($reportDate): void
    {
        $this->reportDate = $reportDate;
    }

    /**
     * @return mixed
     */
    public function getEstimatedCarPrice()
    {
        return $this->estimatedCarPrice;
    }

    /**
     * @param mixed $estimatedCarPrice
     */
    public function setEstimatedCarPrice($estimatedCarPrice): void
    {
        $this->estimatedCarPrice = $estimatedCarPrice;
    }

    /**
     * @return mixed
     */
    public function getRepairAmount()
    {
        return $this->repairAmount;
    }

    /**
     * @param mixed $repairAmount
     */
    public function setRepairAmount($repairAmount): void
    {
        $this->repairAmount = $repairAmount;
    }

    /**
     * @return mixed
     */
    public function getSellingPrice()
    {
        return $this->sellingPrice;
    }

    /**
     * @param mixed $sellingPrice
     */
    public function setSellingPrice($sellingPrice): void
    {
        $this->sellingPrice = $sellingPrice;
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
            $archive->setWreckageReport($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->contains($archive)) {
            $this->archives->removeElement($archive);
            // set the owning side to null (unless already changed)
            if ($archive->getWreckageReport() === $this) {
                $archive->setWreckageReport(null);
            }
        }

        return $this;
    }
}
