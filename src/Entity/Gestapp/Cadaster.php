<?php

namespace App\Entity\Gestapp;

use App\Repository\Gestapp\CadasterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CadasterRepository::class)]
class Cadaster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $parcel = null;

    #[ORM\Column(length: 3)]
    private ?string $Section = null;

    #[ORM\Column(length: 255)]
    private ?string $commune = null;

    #[ORM\Column]
    private ?float $contenance = null;

    #[ORM\ManyToOne(inversedBy: 'cadastre')]
    private ?Property $property = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcel(): ?int
    {
        return $this->parcel;
    }

    public function setParcel(int $parcel): self
    {
        $this->parcel = $parcel;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->Section;
    }

    public function setSection(string $Section): self
    {
        $this->Section = $Section;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getContenance(): ?float
    {
        return $this->contenance;
    }

    public function setContenance(float $contenance): self
    {
        $this->contenance = $contenance;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }
}
