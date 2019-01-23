<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 * @Vich\Uploadable
 */
class Vehicle
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
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $registrationNumber;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="grayCard", fileNameProperty="grayCard", size="imageSize")
     * @Assert\Image()
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $grayCard;
    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\GreaterThan(0)
     */
    private $horsePower;
    /**
     * @ORM\Column(type="date")
     */
    private $dateOfRegistration;
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\GreaterThan(0)
     */
    private $doorsNumber;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     */
    private $color;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ensured", inversedBy="vehicles")
     * @ORM\JoinColumn(nullable=false, name="ensured")
     */
    private $ensured;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Folder", mappedBy="vehicle")
     */
    private $folders;

    public function __construct()
    {
        $this->folders = new ArrayCollection();
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getGrayCard(): ?string
    {
        return $this->grayCard;
    }

    public function setGrayCard(?string $grayCard): void
    {
        $this->grayCard = $grayCard;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }


    public function getHorsePower(): ?string
    {
        return $this->horsePower;
    }

    public function setHorsePower(string $horsePower): self
    {
        $this->horsePower = $horsePower;

        return $this;
    }

    public function getDateOfRegistration(): ?\DateTimeInterface
    {
        return $this->dateOfRegistration;
    }

    public function setDateOfRegistration(\DateTimeInterface $dateOfRegistration): self
    {
        $this->dateOfRegistration = $dateOfRegistration;

        return $this;
    }

    public function getDoorsNumber(): ?string
    {
        return $this->doorsNumber;
    }

    public function setDoorsNumber(string $doorsNumber): self
    {
        $this->doorsNumber = $doorsNumber;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getEnsured(): ?Ensured
    {
        return $this->ensured;
    }

    public function setEnsured(?Ensured $ensured): self
    {
        $this->ensured = $ensured;

        return $this;
    }

    /**
     * @return Collection|Folder[]
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders[] = $folder;
            $folder->setVehicle($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): self
    {
        if ($this->folders->contains($folder)) {
            $this->folders->removeElement($folder);
            // set the owning side to null (unless already changed)
            if ($folder->getVehicle() === $this) {
                $folder->setVehicle(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString()
    {
        return $this->registrationNumber;
    }
}
