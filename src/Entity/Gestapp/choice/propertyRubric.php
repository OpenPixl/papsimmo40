<?php

namespace App\Entity\Gestapp\choice;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\choice\propertyRubricRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: propertyRubricRepository::class)]
class propertyRubric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private ?string $name = null;

    #[ORM\Column(length: 3)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'propertyRubric', targetEntity: propertyRubricss::class)]
    private Collection $rubricss;

    #[ORM\ManyToOne(inversedBy: 'rubric')]
    private ?propertyFamily $propertyFamily = null;

    #[ORM\OneToMany(mappedBy: 'rubric', targetEntity: Property::class)]
    private Collection $properties;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $en = null;

    public function __construct()
    {
        $this->rubricss = new ArrayCollection();
        $this->properties = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, propertyRubricss>
     */
    public function getRubricss(): Collection
    {
        return $this->rubricss;
    }

    public function addRubricss(propertyRubricss $rubricss): self
    {
        if (!$this->rubricss->contains($rubricss)) {
            $this->rubricss->add($rubricss);
            $rubricss->setPropertyRubric($this);
        }

        return $this;
    }

    public function removeRubricss(propertyRubricss $rubricss): self
    {
        if ($this->rubricss->removeElement($rubricss)) {
            // set the owning side to null (unless already changed)
            if ($rubricss->getPropertyRubric() === $this) {
                $rubricss->setPropertyRubric(null);
            }
        }

        return $this;
    }

    public function getPropertyFamily(): ?propertyFamily
    {
        return $this->propertyFamily;
    }

    public function setPropertyFamily(?propertyFamily $propertyFamily): self
    {
        $this->propertyFamily = $propertyFamily;

        return $this;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setRubric($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getRubric() === $this) {
                $property->setRubric(null);
            }
        }

        return $this;
    }

    public function getEn(): ?string
    {
        return $this->en;
    }

    public function setEn(string $en): static
    {
        $this->en = $en;

        return $this;
    }
}
