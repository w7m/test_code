<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpertRepository")
 */
class Expert extends User
{


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $registration_tax_number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $postal_code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Expertise", mappedBy="expert")
     */
    private $expertises;

    public function __construct()
    {
        $this->expertises = new ArrayCollection();
    }

    public function getRegistrationTaxNumber(): ?string
    {
        return $this->registration_tax_number;
    }

    public function setRegistrationTaxNumber(string $registration_tax_number): self
    {
        $this->registration_tax_number = $registration_tax_number;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(int $postal_code): self
    {
        $this->postal_code = $postal_code;

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
            $expertise->setExpert($this);
        }

        return $this;
    }

    public function removeExpertise(Expertise $expertise): self
    {
        if ($this->expertises->contains($expertise)) {
            $this->expertises->removeElement($expertise);
            // set the owning side to null (unless already changed)
            if ($expertise->getExpert() === $this) {
                $expertise->setExpert(null);
            }
        }

        return $this;
    }

}
