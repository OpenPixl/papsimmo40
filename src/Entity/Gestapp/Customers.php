<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\CustomerType;
use App\Repository\Gestapp\CustomersRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Customers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $RefCustomer;

    #[ORM\Column(type: 'string', length: 80)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 80)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 125)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: CustomerType::class)]
    private $CustomerType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $complement;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $zipcode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'boolean')]
    private $isArchived = false;

    #[ORM\OneToMany(mappedBy: 'customers', targetEntity: Contact::class, orphanRemoval: true)]
    private $contacts;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private $refEmployed;

    #[ORM\Column(type: 'datetime_immutable')]
    private $CreatedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'refApplicant')]
    private $applicant;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'refAcquirer')]
    private $acquirer;

    /**
     * Permet d'initialiser le slug !
     * Utilisation de slugify pour transformer une chaine de caractères en slug
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug() {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->firstName."_".$this->lastName);
    }

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefCustomer(): ?string
    {
        return $this->RefCustomer;
    }

    public function setRefCustomer(?string $RefCustomer): self
    {
        $this->RefCustomer = $RefCustomer;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getCustomerType(): ?CustomerType
    {
        return $this->CustomerType;
    }

    public function setCustomerType(?CustomerType $CustomerType): self
    {
        $this->CustomerType = $CustomerType;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCustomers($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCustomers() === $this) {
                $contact->setCustomers(null);
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

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

    public function __toString()
    {
        return $this->lastName." ".$this->lastName;
    }

    public function getApplicant(): ?Project
    {
        return $this->applicant;
    }

    public function setProject(?Project $applicant): self
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getAcquirer(): ?Project
    {
        return $this->acquirer;
    }

    public function setAcquirer(?Project $acquirer): self
    {
        $this->acquirer = $acquirer;

        return $this;
    }
}
