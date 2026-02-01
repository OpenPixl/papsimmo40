<?php

namespace App\Entity\Gestapp\Transaction;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\Transaction\AnnulationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnulationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Annulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'annulation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transaction $transaction = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $supportName = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $supportFact = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $supportFactColl = null;

    #[ORM\Column(length: 100)]
    private ?string $author = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?bool $isValidNotarialDoc = false;

    #[ORM\Column(nullable: true)]
    private ?bool $isValidFactAdmin = false;

    #[ORM\Column(nullable: true)]
    private ?bool $isValidFactColl = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getSupportName(): ?string
    {
        return $this->supportName;
    }

    public function setSupportName(string $supportName = null): static
    {
        $this->supportName = $supportName;

        return $this;
    }

    public function getSupportFact(): ?string
    {
        return $this->supportFact;
    }

    public function setSupportFact(string $supportFact = null): static
    {
        $this->supportFact = $supportFact;

        return $this;
    }

    public function getSupportFactColl(): ?string
    {
        return $this->supportFactColl;
    }

    public function setSupportFactColl(string $supportFactColl = null): static
    {
        $this->supportFactColl = $supportFactColl;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

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

    public function isValidNotarialDoc(): ?bool
    {
        return $this->isValidNotarialDoc;
    }

    public function setIsValidNotarialDoc(?bool $isValidNotarialDoc): static
    {
        $this->isValidNotarialDoc = $isValidNotarialDoc;

        return $this;
    }

    public function isValidFactAdmin(): ?bool
    {
        return $this->isValidFactAdmin;
    }

    public function setIsValidFactAdmin(bool $isValidFactAdmin): static
    {
        $this->isValidFactAdmin = $isValidFactAdmin;

        return $this;
    }

    public function isValidFactColl(): ?bool
    {
        return $this->isValidFactColl;
    }

    public function setIsValidFactColl(?bool $isValidFactColl): static
    {
        $this->isValidFactColl = $isValidFactColl;

        return $this;
    }
}
