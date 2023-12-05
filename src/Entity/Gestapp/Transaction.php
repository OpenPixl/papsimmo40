<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Employed;
use App\Repository\Gestapp\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $state = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Property $property = null;

    #[ORM\ManyToMany(targetEntity: Customer::class, inversedBy: 'transactions')]
    private Collection $customer;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column(length: 25)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAtPromise = null;

    #[ORM\Column(type: 'string')]
    private string $promisePdfFilename;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAtSale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAtKeys = null;

    #[ORM\Column(type: 'string')]
    private string $actePdfFilename;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Employed $refEmployed = null;

    #[ORM\Column]
    private ?bool $isValidPromisepdf = false;

    #[ORM\Column]
    private ?bool $isValidActepdf = false;

    public function __construct()
    {
        $this->customer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

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

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomer(): Collection
    {
        return $this->customer;
    }

    public function addCustomer(Customer $customer): static
    {
        if (!$this->customer->contains($customer)) {
            $this->customer->add($customer);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        $this->customer->removeElement($customer);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDateAtPromise(): ?\DateTimeInterface
    {
        return $this->dateAtPromise;
    }

    public function setDateAtPromise(\DateTimeInterface $dateAtPromise): static
    {
        $this->dateAtPromise = $dateAtPromise;

        return $this;
    }

    public function getPromisePdfFilename(): string
    {
        return $this->promisePdfFilename;
    }

    public function setPromisePdfFilename(string $promisePdfFilename): self
    {
        $this->promisePdfFilename = $promisePdfFilename;

        return $this;
    }

    public function getDateAtSale(): ?\DateTimeInterface
    {
        return $this->dateAtSale;
    }

    public function setDateAtSale(?\DateTimeInterface $dateAtSale): static
    {
        $this->dateAtSale = $dateAtSale;

        return $this;
    }

    public function getDateAtKeys(): ?\DateTimeInterface
    {
        return $this->dateAtKeys;
    }

    public function setDateAtKeys(?\DateTimeInterface $dateAtKeys): static
    {
        $this->dateAtKeys = $dateAtKeys;

        return $this;
    }
    public function getActePdfFilename(): string
    {
        return $this->actePdfFilename;
    }

    public function setActePdfFilename(string $actePdfFilename): self
    {
        $this->actePdfFilename = $actePdfFilename;

        return $this;
    }

    public function getRefEmployed(): ?Employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?Employed $refEmployed): static
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }

    public function isIsValidPromisepdf(): ?bool
    {
        return $this->isValidPromisepdf;
    }

    public function setIsValidPromisepdf(bool $isValidPromisepdf): static
    {
        $this->isValidPromisepdf = $isValidPromisepdf;

        return $this;
    }

    public function isIsValidActepdf(): ?bool
    {
        return $this->isValidActepdf;
    }

    public function setIsValidActepdf(bool $isValidActepdf): static
    {
        $this->isValidActepdf = $isValidActepdf;

        return $this;
    }
}
