<?php

namespace App\Entity\Gestapp;

use App\Repository\Gestapp\AgencyRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgencyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Agency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complement = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $zipcode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 14, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contactEmail = null;

    /**
     * @var Collection<int, AgencyEmployed>
     */
    #[ORM\OneToMany(mappedBy: 'refAgency', targetEntity: AgencyEmployed::class)]
    private Collection $agencyEmployeds;

    #[ORM\Column(length: 255)]
    private ?string $numbercardt = null;

    public function __construct()
    {
        $this->agencyEmployeds = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug() {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): static
    {
        $this->complement = $complement;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return Collection<int, AgencyEmployed>
     */
    public function getAgencyEmployeds(): Collection
    {
        return $this->agencyEmployeds;
    }

    public function addAgencyEmployed(AgencyEmployed $agencyEmployed): static
    {
        if (!$this->agencyEmployeds->contains($agencyEmployed)) {
            $this->agencyEmployeds->add($agencyEmployed);
            $agencyEmployed->setRefAgency($this);
        }

        return $this;
    }

    public function removeAgencyEmployed(AgencyEmployed $agencyEmployed): static
    {
        if ($this->agencyEmployeds->removeElement($agencyEmployed)) {
            // set the owning side to null (unless already changed)
            if ($agencyEmployed->getRefAgency() === $this) {
                $agencyEmployed->setRefAgency(null);
            }
        }

        return $this;
    }

    public function getNumbercardt(): ?string
    {
        return $this->numbercardt;
    }

    public function setNumbercardt(string $numbercardt): static
    {
        $this->numbercardt = $numbercardt;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
