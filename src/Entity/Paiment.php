<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaimentRepository")
 */
class Paiment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $paimentDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Expertise", mappedBy="paiment", orphanRemoval=true)
     */
    private $expertises;

    public function __construct()
    {
        $this->expertises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaimentDate(): ?\DateTimeInterface
    {
        return $this->paimentDate;
    }

    public function setPaimentDate(\DateTimeInterface $paimentDate): self
    {
        $this->paimentDate = $paimentDate;

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
            $expertise->setPaiment($this);
        }

        return $this;
    }

    public function removeExpertise(Expertise $expertise): self
    {
        if ($this->expertises->contains($expertise)) {
            $this->expertises->removeElement($expertise);
            // set the owning side to null (unless already changed)
            if ($expertise->getPaiment() === $this) {
                $expertise->setPaiment(null);
            }
        }

        return $this;
    }
}
