<?php

namespace App\Entity\Cart;

use App\Entity\Admin\Employed;
use App\Repository\Cart\CartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    private ?Product $RefProduct = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    private ?Employed $RefEmployed = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    private ?CategoryProduct $productCat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productName = null;

    #[ORM\Column(nullable: true)]
    private ?int $productQty = null;

    #[ORM\Column(nullable: true)]
    private ?int $productId = null;

    #[ORM\Column(nullable: true)]
    private ?int $item = null;

    #[ORM\Column(type: Types::GUID, nullable: true)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $productRef = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefProduct(): ?Product
    {
        return $this->RefProduct;
    }

    public function setRefProduct(?Product $RefProduct): static
    {
        $this->RefProduct = $RefProduct;

        return $this;
    }

    public function getRefEmployed(): ?Employed
    {
        return $this->RefEmployed;
    }

    public function setRefEmployed(?Employed $RefEmployed): static
    {
        $this->RefEmployed = $RefEmployed;

        return $this;
    }

    public function getProductCat(): ?CategoryProduct
    {
        return $this->productCat;
    }

    public function setProductCat(?CategoryProduct $productCat): static
    {
        $this->productCat = $productCat;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductQty(): ?int
    {
        return $this->productQty;
    }

    public function setProductQty(?int $productQty): static
    {
        $this->productQty = $productQty;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getItem(): ?int
    {
        return $this->item;
    }

    public function setItem(?int $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getProductRef(): ?string
    {
        return $this->productRef;
    }

    public function setProductRef(string $productRef): static
    {
        $this->productRef = $productRef;

        return $this;
    }
}
