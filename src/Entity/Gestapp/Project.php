<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\MandateType;
use App\Repository\Gestapp\ProjectRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $refMandate;

    #[ORM\Column(type: 'string', length: 100)]
    private $slug;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Customers::class)]
    private $refApplicant;

    #[ORM\OneToMany(mappedBy: 'acquirer', targetEntity: Customers::class)]
    private $refAcquirer;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'projects')]
    private $refEmployed;

    #[ORM\ManyToOne(targetEntity: MandateType::class)]
    private $mandateType;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $state;

    #[ORM\Column(type: 'text', nullable: true)]
    private $notes;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $mandateLength;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    /**
     * Permet d'initialiser le slug !
     * Utilisation de slugify pour transformer une chaine de caractères en slug
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug() {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->refMandate);
    }

    public function __construct()
    {
        $this->refApplicant = new ArrayCollection();
        $this->refAcquirer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefMandate(): ?string
    {
        return $this->refMandate;
    }

    public function setRefMandate(string $refMandate): self
    {
        $this->refMandate = $refMandate;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Customers>
     */
    public function getRefApplicant(): Collection
    {
        return $this->refApplicant;
    }

    public function addRefApplicant(Customers $refApplicant): self
    {
        if (!$this->refApplicant->contains($refApplicant)) {
            $this->refApplicant[] = $refApplicant;
            $refApplicant->setProject($this);
        }

        return $this;
    }

    public function removeRefApplicant(Customers $refApplicant): self
    {
        if ($this->refApplicant->removeElement($refApplicant)) {
            // set the owning side to null (unless already changed)
            if ($refApplicant->getProject() === $this) {
                $refApplicant->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Customers>
     */
    public function getRefAcquirer(): Collection
    {
        return $this->refAcquirer;
    }

    public function addRefAcquirer(Customers $refAcquirer): self
    {
        if (!$this->refAcquirer->contains($refAcquirer)) {
            $this->refAcquirer[] = $refAcquirer;
            $refAcquirer->setAcquirer($this);
        }

        return $this;
    }

    public function removeRefAcquirer(Customers $refAcquirer): self
    {
        if ($this->refAcquirer->removeElement($refAcquirer)) {
            // set the owning side to null (unless already changed)
            if ($refAcquirer->getAcquirer() === $this) {
                $refAcquirer->setAcquirer(null);
            }
        }

        return $this;
    }

    public function getRefEmployed(): ?Employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?Employed $refEmployed): self
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }

    public function getMandateType(): ?MandateType
    {
        return $this->mandateType;
    }

    public function setMandateType(?MandateType $mandateType): self
    {
        $this->mandateType = $mandateType;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getMandateLength(): ?string
    {
        return $this->mandateLength;
    }

    public function setMandateLength(?string $mandateLength): self
    {
        $this->mandateLength = $mandateLength;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
