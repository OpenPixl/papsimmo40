<?php

namespace App\Entity\Gestapp;

use App\Repository\Gestapp\PublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $isWebpublish = false;

    #[ORM\Column(type: 'boolean')]
    private $isSocialNetwork = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $sector;

    #[ORM\Column]
    private ?bool $isPublishParven = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsWebpublish(): ?bool
    {
        return $this->isWebpublish;
    }

    public function setIsWebpublish(bool $isWebpublish): self
    {
        $this->isWebpublish = $isWebpublish;

        return $this;
    }

    public function getIsSocialNetwork(): ?bool
    {
        return $this->isSocialNetwork;
    }

    public function setIsSocialNetwork(bool $isSocialNetwork): self
    {
        $this->isSocialNetwork = $isSocialNetwork;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function isIsPublishParven(): ?bool
    {
        return $this->isPublishParven;
    }

    public function setIsPublishParven(bool $isPublishParven): self
    {
        $this->isPublishParven = $isPublishParven;

        return $this;
    }
}
