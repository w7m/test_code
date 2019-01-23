<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InsurerRepository")
 */
class Insurer extends User
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $insurerId;

    public function getInsurerId(): ?string
    {
        return $this->insurerId;
    }

    public function setInsurerId(string $insurerId): self
    {
        $this->insurerId = $insurerId;

        return $this;
    }
}
