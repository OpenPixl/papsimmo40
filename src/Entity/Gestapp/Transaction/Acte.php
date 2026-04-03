<?php

namespace App\Entity\Gestapp\Transaction;

use App\Entity\Enum\Transaction\ActeName;
use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\Transaction\ActeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ActeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Acte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: ActeName::class)]
    private ?acteName $acteName = null;

    #[ORM\Column(type:'string', nullable: true)]
    private $acteFilename;

    #[ORM\Column(type:'integer', nullable: true)]
    private $acteFilesize;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'actes')]
    private ?Transaction $transaction = null;

    public function getActeName(): ?ActeName
    {
        return $this->acteName;
    }

    public function setActeName(ActeName $acteName): static
    {
        $this->acteName = $acteName;

        return $this;
    }

    public function setActeFilename(string $acteFilename = null): self
    {
        $this->acteFilename = $acteFilename;

        return $this;
    }

    public function getActeFilename(): ?string
    {
        return $this->acteFilename;
    }

    public function setActeFilesize(?int $acteFilesize): void
    {
        $this->acteFilesize = $acteFilesize;
    }

    public function getActeFilesize(): ?int
    {
        return $this->acteFilesize;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

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

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function __toString(){

        return $this->fileName;
    }
}
