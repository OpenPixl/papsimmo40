<?php

namespace App\Entity\Gestapp;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\CustomerChoice;
use App\Repository\Gestapp\CustomerRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpseclib3\Crypt\EC\Formats\Keys\OpenSSH;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ["first_name", "last_name"], name: 'customer_idx', flags: ['fulltext'])]
#[ApiResource(
    shortName: 'Client',
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: 'employed/{id}/clients',
            uriVariables: [
                'id' => new Link(fromProperty: 'Customer' , fromClass: Employed::class)
            ],
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => 'Obtenir la liste des clients selon le mandataire.',
                'description' => 'Obtenir la liste des clients selon le mandataire.'
            ],
            normalizationContext: ['groups' => 'client:item']
        ),
        new Get(),
        new Post(),
        new Patch(
            uriTemplate: 'client/{id}/update'
        ),
        new Delete()
    ]
)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    #[Groups(['client:list', 'client:write:patch' ,'client:item', 'transaction:item'])]
    private $RefCustomer;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    #[Groups(['client:list', 'client:write:patch' , 'client:item', 'transaction:item'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' , 'client:item', 'transaction:item'])]
    private $lastName;

    #[ORM\Column(type: 'string', length: 125)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: CustomerChoice::class)]
    private $customerChoice;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['customer:list', 'client:write:edit' ,'customer:item', 'transaction:item'])]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $complement;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $zipcode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $city;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $home;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $desk;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $gsm;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $otherEmail;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private ?\DateTimeInterface $ddn = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private ?string $ddnIn = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $facebook;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $instagram;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $linkedin;

    #[ORM\Column(type: 'boolean')]
    private $isArchived = false;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'Customer')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['client:list', 'client:write:edit' ,'client:item', 'transaction:item'])]
    private $refEmployed;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Property::class, mappedBy: 'Customer')]
    private $properties;

    #[ORM\ManyToMany(targetEntity: Transaction::class, mappedBy: 'customer')]
    private Collection $transactions;

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
        $this->properties = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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

    public function getCustomerChoice(): ?CustomerChoice
    {
        return $this->customerChoice;
    }

    public function setCustomerChoice(?CustomerChoice $customerChoice): self
    {
        $this->customerChoice = $customerChoice;

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

    public function getHome(): ?string
    {
        return $this->home;
    }

    public function setHome(?string $home): self
    {
        $this->home = $home;

        return $this;
    }

    public function getDesk(): ?string
    {
        return $this->desk;
    }

    public function setDesk(?string $desk): self
    {
        $this->desk = $desk;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(string $gsm): self
    {
        $this->gsm = $gsm;

        return $this;
    }

    public function getOtherEmail(): ?string
    {
        return $this->otherEmail;
    }

    public function setOtherEmail(?string $otherEmail): self
    {
        $this->otherEmail = $otherEmail;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

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

    public function getRefEmployed(): ?Employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?Employed $refEmployed): self
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime('now');

        return $this;
    }

    public function __toString()
    {
        return $this->firstName." ".$this->lastName;
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
            $this->properties[] = $property;
            $property->addCustomer($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            $property->removeCustomer($this);
        }

        return $this;
    }

    public function getDdn(): ?\DateTimeInterface
    {
        return $this->ddn;
    }

    public function setDdn(?\DateTimeInterface $ddn): self
    {
        $this->ddn = $ddn;

        return $this;
    }

    public function getDdnIn(): ?string
    {
        return $this->ddnIn;
    }

    public function setDdnIn(?string $ddnIn): self
    {
        $this->ddnIn = $ddnIn;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->addCustomer($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            $transaction->removeCustomer($this);
        }

        return $this;
    }
}
