<?php

namespace App\Entity\Gestapp\Customer;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Entity\Gestapp\Customer;
use App\Repository\Gestapp\Customer\ResearchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Research
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'research')]
    private ?Customer $customer = null;

    #[ORM\Column(length: 20)]
    private ?string $researchFor = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $typeProject = null;

    #[ORM\Column(nullable: true)]
    private ?int $budgetMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $budgetMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $pieceMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $pieceMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $roomMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $roomMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $surfaceMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $surfaceMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $surfaceLandMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $surfaceLandMax = null;

    /**
     * @var Collection<int, PropertyEnergy>
     */
    #[ORM\ManyToMany(targetEntity: PropertyEnergy::class, inversedBy: 'research')]
    private Collection $energies;

    /**
     * @var Collection<int, ResearchOptions>
     */
    #[ORM\ManyToMany(targetEntity: ResearchOptions::class, inversedBy: 'research')]
    private Collection $options;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'research')]
    private ?ResearchBien $researchBien = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->energies = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getResearchFor(): ?string
    {
        return $this->researchFor;
    }

    public function setResearchFor(string $researchFor): static
    {
        $this->researchFor = $researchFor;

        return $this;
    }

    public function getTypeProject(): ?string
    {
        return $this->typeProject;
    }

    public function setTypeProject(?string $typeProject): static
    {
        $this->typeProject = $typeProject;

        return $this;
    }

    public function getBudgetMin(): ?int
    {
        return $this->budgetMin;
    }

    public function setBudgetMin(?int $budgetMin): static
    {
        $this->budgetMin = $budgetMin;

        return $this;
    }

    public function getBudgetMax(): ?int
    {
        return $this->budgetMax;
    }

    public function setBudgetMax(?int $budgetMax): static
    {
        $this->budgetMax = $budgetMax;

        return $this;
    }

    public function getPieceMin(): ?int
    {
        return $this->pieceMin;
    }

    public function setPieceMin(?int $pieceMin): static
    {
        $this->pieceMin = $pieceMin;

        return $this;
    }

    public function getPieceMax(): ?int
    {
        return $this->pieceMax;
    }

    public function setPieceMax(?int $pieceMax): static
    {
        $this->pieceMax = $pieceMax;

        return $this;
    }

    public function getRoomMin(): ?int
    {
        return $this->roomMin;
    }

    public function setRoomMin(?int $roomMin): static
    {
        $this->roomMin = $roomMin;

        return $this;
    }

    public function getRoomMax(): ?int
    {
        return $this->roomMax;
    }

    public function setRoomMax(?int $roomMax): static
    {
        $this->roomMax = $roomMax;

        return $this;
    }

    public function getSurfaceMin(): ?int
    {
        return $this->surfaceMin;
    }

    public function setSurfaceMin(?int $surfaceMin): static
    {
        $this->surfaceMin = $surfaceMin;

        return $this;
    }

    public function getSurfaceMax(): ?int
    {
        return $this->surfaceMax;
    }

    public function setSurfaceMax(?int $surfaceMax): static
    {
        $this->surfaceMax = $surfaceMax;

        return $this;
    }

    public function getSurfaceLandMin(): ?int
    {
        return $this->surfaceLandMin;
    }

    public function setSurfaceLandMin(?int $surfaceLandMin): static
    {
        $this->surfaceLandMin = $surfaceLandMin;

        return $this;
    }

    public function getSurfaceLandMax(): ?int
    {
        return $this->surfaceLandMax;
    }

    public function setSurfaceLandMax(?int $surfaceLandMax): static
    {
        $this->surfaceLandMax = $surfaceLandMax;

        return $this;
    }

    /**
     * @return Collection<int, PropertyEnergy>
     */
    public function getEnergies(): Collection
    {
        return $this->energies;
    }

    public function addEnergy(PropertyEnergy $energy): static
    {
        if (!$this->energies->contains($energy)) {
            $this->energies->add($energy);
        }

        return $this;
    }

    public function removeEnergy(PropertyEnergy $energy): static
    {
        $this->energies->removeElement($energy);

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

    /**
     * @return Collection<int, ResearchOptions>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(ResearchOptions $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    public function removeOption(ResearchOptions $option): static
    {
        $this->options->removeElement($option);

        return $this;
    }

    public function getResearchBien(): ?ResearchBien
    {
        return $this->researchBien;
    }

    public function setResearchBien(?ResearchBien $researchBien): static
    {
        $this->researchBien = $researchBien;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
