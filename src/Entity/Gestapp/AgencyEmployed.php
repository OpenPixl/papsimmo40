<?php

namespace App\Entity\Gestapp;

use App\Repository\Gestapp\AgencyEmployedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgencyEmployedRepository::class)]
class AgencyEmployed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(inversedBy: 'agencyEmployeds')]
    private ?Agency $refAgency = null;

    #[ORM\ManyToOne(inversedBy: 'agencyEmployeds')]
    private ?Transaction $refTransaction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRefAgency(): ?Agency
    {
        return $this->refAgency;
    }

    public function setRefAgency(?Agency $refAgency): static
    {
        $this->refAgency = $refAgency;

        return $this;
    }

    public function getRefTransaction(): ?Transaction
    {
        return $this->refTransaction;
    }

    public function setRefTransaction(?Transaction $refTransaction): static
    {
        $this->refTransaction = $refTransaction;

        return $this;
    }
}
