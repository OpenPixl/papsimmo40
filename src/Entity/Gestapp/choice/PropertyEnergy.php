<?php

namespace App\Entity\Gestapp\choice;

use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Customer\Research;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyEnergyRepository::class)]
class PropertyEnergy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $name;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $slCode = null;

    /**
     * @var Collection<int, Complement>
     */
    #[ORM\ManyToMany(targetEntity: Complement::class, mappedBy: 'energies')]
    private Collection $complements;

    /**
     * @var Collection<int, Research>
     */
    #[ORM\ManyToMany(targetEntity: Research::class, mappedBy: 'energies')]
    private Collection $research;

    public function __construct()
    {
        $this->complements = new ArrayCollection();
        $this->research = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(){
        return $this->name;
    }

    public function getSlCode(): ?string
    {
        return $this->slCode;
    }

    public function setSlCode(?string $slCode): self
    {
        $this->slCode = $slCode;

        return $this;
    }

    /**
     * @return Collection<int, Complement>
     */
    public function getComplements(): Collection
    {
        return $this->complements;
    }

    public function addComplement(Complement $complement): static
    {
        if (!$this->complements->contains($complement)) {
            $this->complements->add($complement);
            $complement->addEnergy($this);
        }

        return $this;
    }

    public function removeComplement(Complement $complement): static
    {
        if ($this->complements->removeElement($complement)) {
            $complement->removeEnergy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Research>
     */
    public function getResearch(): Collection
    {
        return $this->research;
    }

    public function addResearch(Research $research): static
    {
        if (!$this->research->contains($research)) {
            $this->research->add($research);
            $research->addEnergy($this);
        }

        return $this;
    }

    public function removeResearch(Research $research): static
    {
        if ($this->research->removeElement($research)) {
            $research->removeEnergy($this);
        }

        return $this;
    }
}
