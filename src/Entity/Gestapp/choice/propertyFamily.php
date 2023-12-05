<?php

namespace App\Entity\Gestapp\choice;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\choice\propertyFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: propertyFamilyRepository::class)]
class propertyFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private ?string $name = null;

    #[ORM\Column(length: 3)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'propertyFamily', targetEntity: propertyRubric::class)]
    private Collection $rubric;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: Property::class)]
    private Collection $properties;

    public function __construct()
    {
        $this->rubric = new ArrayCollection();
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
     * @return Collection<int, propertyRubric>
     */
    public function getRubric(): Collection
    {
        return $this->rubric;
    }

    public function addRubric(propertyRubric $rubric): self
    {
        if (!$this->rubric->contains($rubric)) {
            $this->rubric->add($rubric);
            $rubric->setPropertyFamily($this);
        }

        return $this;
    }

    public function removeRubric(propertyRubric $rubric): self
    {
        if ($this->rubric->removeElement($rubric)) {
            // set the owning side to null (unless already changed)
            if ($rubric->getPropertyFamily() === $this) {
                $rubric->setPropertyFamily(null);
            }
        }

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
            $property->setFamily($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getFamily() === $this) {
                $property->setFamily(null);
            }
        }

        return $this;
    }
}
