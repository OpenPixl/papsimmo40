<?php

namespace App\Entity\Gestapp\Customer;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Gestapp\Customer\ResearchOptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchOptionsRepository::class)]
#[ApiResource]
class ResearchOptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Research>
     */
    #[ORM\ManyToMany(targetEntity: Research::class, mappedBy: 'options')]
    private Collection $research;

    public function __construct()
    {
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

    public function setName(?string $name): static
    {
        $this->name = $name;

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
            $research->addOption($this);
        }

        return $this;
    }

    public function removeResearch(Research $research): static
    {
        if ($this->research->removeElement($research)) {
            $research->removeOption($this);
        }

        return $this;
    }
}
