<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParameterRepository")
 */
class Parameter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $photoPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $openingFileExpense;

    /**
     * @ORM\Column(type="float")
     */
    private $expertiseFees;

    /**
     * @ORM\Column(type="float")
     */
    private $billPercentage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $insuranceCompanyName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $insurerAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $insurerCity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postcodeInsurer;

    /**
     * @return mixed
     */
    public function getInsurancePhone()
    {
        return $this->insurancePhone;
    }

    /**
     * @param mixed $insurancePhone
     */
    public function setInsurancePhone($insurancePhone): void
    {
        $this->insurancePhone = $insurancePhone;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $insurancePhone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoPrice(): ?float
    {
        return $this->photoPrice;
    }

    public function setPhotoPrice(float $photoPrice): self
    {
        $this->photoPrice = $photoPrice;

        return $this;
    }

    public function getOpeningFileExpense(): ?float
    {
        return $this->openingFileExpense;
    }

    public function setOpeningFileExpense(float $openingFileExpense): self
    {
        $this->openingFileExpense = $openingFileExpense;

        return $this;
    }

    public function getExpertiseFees(): ?float
    {
        return $this->expertiseFees;
    }

    public function setExpertiseFees(float $expertiseFees): self
    {
        $this->expertiseFees = $expertiseFees;

        return $this;
    }

    public function getBillPercentage(): ?float
    {
        return $this->billPercentage;
    }

    public function setBillPercentage(float $billPercentage): self
    {
        $this->billPercentage = $billPercentage;

        return $this;
    }

    public function getInsuranceCompanyName(): ?string
    {
        return $this->insuranceCompanyName;
    }

    public function setInsuranceCompanyName(string $insuranceCompanyName): self
    {
        $this->insuranceCompanyName = $insuranceCompanyName;

        return $this;
    }

    public function getInsurerAddress(): ?string
    {
        return $this->insurerAddress;
    }

    public function setInsurerAddress(string $insurerAddress): self
    {
        $this->insurerAddress = $insurerAddress;

        return $this;
    }

    public function getInsurerCity(): ?string
    {
        return $this->insurerCity;
    }

    public function setInsurerCity(string $insurerCity): self
    {
        $this->insurerCity = $insurerCity;

        return $this;
    }

    public function getPostcodeInsurer(): ?string
    {
        return $this->postcodeInsurer;
    }

    public function setPostcodeInsurer(string $postcodeInsurer): self
    {
        $this->postcodeInsurer = $postcodeInsurer;

        return $this;
    }

}
