<?php

namespace App\Entity\Gestapp\Customer;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Gestapp\Customer\ResearchBienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchBienRepository::class)]
#[ApiResource]
class ResearchBien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    /**
     * @var Collection<int, Research>
     */
    #[ORM\OneToMany(mappedBy: 'researchBien', targetEntity: Research::class)]
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

    public function setName(string $name): static
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
            $research->setResearchBien($this);
        }

        return $this;
    }

    public function removeResearch(Research $research): static
    {
        if ($this->research->removeElement($research)) {
            // set the owning side to null (unless already changed)
            if ($research->getResearchBien() === $this) {
                $research->setResearchBien(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->name;
    }
}
