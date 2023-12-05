<?php

namespace App\Entity\Gestapp\choice;

use App\Entity\Gestapp\Complement;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PropertyEquipementRepository::class)]
class PropertyEquipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $name;

    #[ORM\ManyToMany(targetEntity: Complement::class, mappedBy: 'propertyEquipment')]
    private $complements;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Cat;

    public function __construct()
    {
        $this->complements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Complement>
     */
    public function getComplements(): Collection
    {
        return $this->complements;
    }

    public function addComplement(Complement $complement): self
    {
        if (!$this->complements->contains($complement)) {
            $this->complements[] = $complement;
            $complement->addPropertyEquipment($this);
        }

        return $this;
    }

    public function removeComplement(Complement $complement): self
    {
        if ($this->complements->removeElement($complement)) {
            $complement->removePropertyEquipment($this);
        }

        return $this;
    }
    public function __toString(){
        return $this->name;
    }

    public function getCat(): ?string
    {
        return $this->Cat;
    }

    public function setCat(?string $Cat): self
    {
        $this->Cat = $Cat;

        return $this;
    }
}
