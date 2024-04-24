<?php

namespace App\Entity\Gestapp\Transaction;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\Transaction\AddCollTransacRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddCollTransacRepository::class)]
class AddCollTransac
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addCollTransacs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employed $refemployed = null;

    #[ORM\ManyToOne(inversedBy: 'addCollTransacs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transaction $refTransac = null;

    #[ORM\Column(nullable: true)]
    private ?int $pourcentComm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefemployed(): ?Employed
    {
        return $this->refemployed;
    }

    public function setRefemployed(?Employed $refemployed): static
    {
        $this->refemployed = $refemployed;

        return $this;
    }

    public function getRefTransac(): ?Transaction
    {
        return $this->refTransac;
    }

    public function setRefTransac(?Transaction $refTransac): static
    {
        $this->refTransac = $refTransac;

        return $this;
    }

    public function getPourcentComm(): ?int
    {
        return $this->pourcentComm;
    }

    public function setPourcentComm(?int $pourcentComm): static
    {
        $this->pourcentComm = $pourcentComm;

        return $this;
    }
}
