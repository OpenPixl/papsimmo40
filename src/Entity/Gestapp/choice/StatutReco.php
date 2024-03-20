<?php

namespace App\Entity\Gestapp\choice;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Gestapp\choice\StatutRecoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatutRecoRepository::class)]
#[ApiResource()]
class StatutReco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $fr = null;

    #[ORM\Column(nullable: true)]
    private ?int $step = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $note = null;

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

    public function getFr(): ?string
    {
        return $this->fr;
    }

    public function setFr(?string $fr): static
    {
        $this->fr = $fr;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(?int $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function __Tostring()
    {
        return $this->name;
    }
}
