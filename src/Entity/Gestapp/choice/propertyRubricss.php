<?php

namespace App\Entity\Gestapp\choice;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\choice\propertyRubricssRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: propertyRubricssRepository::class)]
class propertyRubricss
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'rubricss')]
    private ?propertyRubric $propertyRubric = null;

    #[ORM\OneToMany(mappedBy: 'rubricss', targetEntity: Property::class)]
    private Collection $properties;

    public function __construct()
    {
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPropertyRubric(): ?propertyRubric
    {
        return $this->propertyRubric;
    }

    public function setPropertyRubric(?propertyRubric $propertyRubric): self
    {
        $this->propertyRubric = $propertyRubric;

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
            $property->setRubricss($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getRubricss() === $this) {
                $property->setRubricss(null);
            }
        }

        return $this;
    }
}
