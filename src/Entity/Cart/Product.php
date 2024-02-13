<?php

namespace App\Entity\Cart;

use App\Repository\Cart\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\ManyToOne]
    private ?CategoryProduct $Category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $visualFilename = null;

    #[ORM\Column(nullable: true)]
    private ?int $visualSize = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $visualExt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): static
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?CategoryProduct
    {
        return $this->Category;
    }

    public function setCategory(?CategoryProduct $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    public function getVisualFilename(): ?string
    {
        return $this->visualFilename;
    }

    public function setVisualFilename(?string $visualFilename): static
    {
        $this->visualFilename = $visualFilename;

        return $this;
    }

    public function getVisualSize(): ?int
    {
        return $this->visualSize;
    }

    public function setVisualSize(?int $visualSize): static
    {
        $this->visualSize = $visualSize;

        return $this;
    }

    public function getVisualExt(): ?string
    {
        return $this->visualExt;
    }

    public function setVisualExt(?string $visualExt): static
    {
        $this->visualExt = $visualExt;

        return $this;
    }
}
