<?php

namespace App\Entity\Gestapp\Property;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\Property\AvenantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvenantRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Avenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avenants')]
    private ?Property $property = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAvenant = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?string $honoraires = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?string $priceFai = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt = null;

    #[ORM\Column]
    private ?bool $isFirstAvenant = true;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $avenantName = null;

    #[ORM\Column(nullable: true)]
    private ?int $avenantSize = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $avenantExt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pathDir = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAvenant(): ?\DateTimeInterface
    {
        return $this->dateAvenant;
    }

    public function setDateAvenant(\DateTimeInterface $dateAvenant): static
    {
        $this->dateAvenant = $dateAvenant;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getHonoraires(): ?string
    {
        return $this->honoraires;
    }

    public function setHonoraires(string $honoraires): static
    {
        $this->honoraires = $honoraires;

        return $this;
    }

    public function getPriceFai(): ?string
    {
        return $this->priceFai;
    }

    public function setPriceFai(string $priceFai): static
    {
        $this->priceFai = $priceFai;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime('now');

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function isFirstAvenant(): ?bool
    {
        return $this->isFirstAvenant;
    }

    public function setIsFirstAvenant(bool $isFirstAvenant): static
    {
        $this->isFirstAvenant = $isFirstAvenant;

        return $this;
    }

    public function getAvenantName(): ?string
    {
        return $this->avenantName;
    }

    public function setAvenantName(?string $avenantName): static
    {
        $this->avenantName = $avenantName;

        return $this;
    }

    public function getAvenantSize(): ?int
    {
        return $this->avenantSize;
    }

    public function setAvenantSize(?int $avenantSize): static
    {
        $this->avenantSize = $avenantSize;

        return $this;
    }

    public function getAvenantExt(): ?string
    {
        return $this->avenantExt;
    }

    public function setAvenantExt(?string $avenantExt): static
    {
        $this->avenantExt = $avenantExt;

        return $this;
    }

    public function getPathDir(): ?string
    {
        return $this->pathDir;
    }

    public function setPathDir(string $pathDir): static
    {
        $this->pathDir = $pathDir;

        return $this;
    }
}
