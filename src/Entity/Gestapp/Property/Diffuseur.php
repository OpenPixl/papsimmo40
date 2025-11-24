<?php

namespace App\Entity\Gestapp\Property;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Gestapp\Property\DiffuseurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiffuseurRepository::class)]
#[ApiResource]
class Diffuseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomDiffuseur = null;

    #[ORM\Column]
    private ?bool $isActiv = null;

    #[ORM\Column(length: 255)]
    private ?string $typeDiffusion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDiffuseur(): ?string
    {
        return $this->nomDiffuseur;
    }

    public function setNomDiffuseur(string $nomDiffuseur): static
    {
        $this->nomDiffuseur = $nomDiffuseur;

        return $this;
    }

    public function isActiv(): ?bool
    {
        return $this->isActiv;
    }

    public function setIsActiv(bool $isActiv): static
    {
        $this->isActiv = $isActiv;

        return $this;
    }

    public function getTypeDiffusion(): ?string
    {
        return $this->typeDiffusion;
    }

    public function setTypeDiffusion(string $typeDiffusion): static
    {
        $this->typeDiffusion = $typeDiffusion;

        return $this;
    }
}
