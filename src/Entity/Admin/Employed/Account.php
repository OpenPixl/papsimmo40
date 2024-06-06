<?php

namespace App\Entity\Admin;

use App\Repository\Admin\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    private ?Employed $refEmployed = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    private ?AccountName $name = null;

    #[ORM\Column(length: 100)]
    private ?string $login = null;

    #[ORM\Column(length: 100)]
    private ?string $p√assword = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?AccountName
    {
        return $this->name;
    }

    public function setName(?AccountName $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getP√assword(): ?string
    {
        return $this->p√assword;
    }

    public function setP√assword(string $p√assword): static
    {
        $this->p√assword = $p√assword;

        return $this;
    }
}
