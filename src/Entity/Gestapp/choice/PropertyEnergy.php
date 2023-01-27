<?php

namespace App\Entity\Gestapp\choice;

use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyEnergyRepository::class)]
class PropertyEnergy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $slCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(){
        return $this->name;
    }

    public function getSlCode(): ?string
    {
        return $this->slCode;
    }

    public function setSlCode(?string $slCode): self
    {
        $this->slCode = $slCode;

        return $this;
    }
}
