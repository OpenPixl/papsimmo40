<?php

namespace App\Entity\Cart;

use App\Repository\Cart\ProductRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: 'text', nullable: true)]
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

    #[ORM\Column(length: 255)]
    private ?string $ref = null;

    #[ORM\OneToMany(mappedBy: 'refProduct', targetEntity: Cart::class)]
    private Collection $carts;


    public function __construct()
    {
        $this->carts = new ArrayCollection();
    }


    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug() {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);
    }

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

    public function setDescriptif(?string $descriptif): self
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


    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setProductRef($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getProductRef() === $this) {
                $cart->setProductRef(null);
            }
        }

        return $this;
    }
}
