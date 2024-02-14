<?php

namespace App\Entity\Cart;

use App\Repository\Cart\PurchaseItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseItemRepository::class)]
class PurchaseItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $productPrice = null;

    #[ORM\Column(nullable: true)]
    private ?float $productQty = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalItem = null;

    #[ORM\ManyToOne]
    private ?purchase $purchase = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

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

    public function getProductPrice(): ?string
    {
        return $this->productPrice;
    }

    public function setProductPrice(?string $productPrice): static
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getProductQty(): ?float
    {
        return $this->productQty;
    }

    public function setProductQty(?float $productQty): static
    {
        $this->productQty = $productQty;

        return $this;
    }

    public function getTotalItem(): ?float
    {
        return $this->totalItem;
    }

    public function setTotalItem(?float $totalItem): static
    {
        $this->totalItem = $totalItem;

        return $this;
    }

    public function getPurchase(): ?purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?purchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }
}
