<?php

namespace App\Entity\Gestapp;

use ApiPlatform\Metadata\Link;
use App\Controller\Api\Admin\Employed\AddEmployed;
use App\Entity\Admin\Contact;
use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Entity\Gestapp\choice\PropertySscategory;
use App\Entity\Gestapp\choice\propertyFamily;
use App\Entity\Gestapp\choice\propertyRubric;
use App\Entity\Gestapp\choice\propertyRubricss;
use App\Repository\Gestapp\PropertyRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ["ref", "name", "zipcode", "city"], name: 'property_idx', flags: ['fulltext'])]
#[ApiResource(
    shortName: 'Propriete',
    operations: [
        new Get(
            normalizationContext: ['groups' => 'property:item']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'property:list']
        ),
        new GetCollection(
            uriTemplate: '/collaborateur/{id}/proprietes',
            uriVariables: [
                'id' => new Link(fromProperty: 'properties' , fromClass: Employed::class)
            ],
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => "Obtenir toutes les propriétés d'un mandataires.",
                'description' => "Obtenir toutes les propriétés d'un mandataires.",
            ],
            normalizationContext: ['groups' => 'property:list']
        ),
        new Post(
            uriTemplate: '/propriete',
            controller: AddEmployed::class,
            openapiContext: [
                'summary' => "Ajouter une propriété",
                'description' => "Ajouter une propriété",
            ],
            normalizationContext: ['groups' => ['property:write:post']]
        ),
        new Patch(
            uriTemplate: '/propriete/{id}/update',
            normalizationContext: ['groups' => ['property:write:patch']],
        )
    ],
    normalizationContext: ['groups' => 'property:list'],
    denormalizationContext: ['groups' => 'property:write'],
    paginationEnabled: false
)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'property:write:post', 'reco:item'])]
    private $ref;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'properties')]
    private $refEmployed;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $annonce;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $piece;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $room;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $surfaceLand;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $surfaceHome;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $dpeAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $diagDpe;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $diagGes;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $complement;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $zipcode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $city;

    #[ORM\OneToOne(targetEntity: Complement::class, cascade: ['persist', 'remove'])]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $options;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Customer::class, inversedBy: 'properties')]
    private $Customer;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['property:list', 'property:item'])]
    private $isIncreating = true;

    #[ORM\OneToOne(targetEntity: Publication::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $publication;

    #[ORM\Column(type: 'integer')]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $reflastnumber;

    #[ORM\Column(type: 'string', length: 14)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $refnumdate;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['property:list', 'property:item', 'property:write:patch'])]
    private $RefMandat;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $dpeEstimateEnergyDown;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $dpeEstimateEnergyUp;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $constructionAt;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Photo::class)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $photos;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $price;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0')]
    private $honoraires;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $priceFai;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['property:list', 'property:item', 'property:write:patch', 'reco:item'])]
    private $eeaYear;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $numberAvenant;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dateAvenant;

    #[ORM\Column]
    private ?bool $isWithExclusivity = false;

    #[ORM\Column]
    private ?bool $isWithoutExclusivity = false;

    #[ORM\Column]
    private ?bool $isSemiExclusivity = false;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private ?PropertyDefinition $propertyDefinition = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Cadaster::class)]
    private Collection $cadastre;

    #[ORM\Column(length: 50)]
    private ?string $projet = null;

    #[ORM\ManyToOne]
    private ?PropertySscategory $sscategory = null;

    #[ORM\Column(length: 20)]
    private ?string $diagChoice = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Contact::class)]
    private Collection $contacts;

    #[ORM\Column]
    private ?bool $isArchived = false;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $mandatAt = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $dupMandat = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private ?propertyFamily $family = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private ?propertyRubric $rubric = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private ?propertyRubricss $rubricss = null;

    #[ORM\Column]
    private ?bool $isNomandat = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEndmandat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $archivedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annonceSlug = null;

    #[ORM\Column(nullable: true)]
    private ?int $rent = null;

    #[ORM\Column(nullable: true)]
    private ?int $rentCharge = null;

    #[ORM\Column]
    private ?bool $isTransaction = false;

    #[ORM\Column(nullable: true)]
    private ?int $rentChargeModsPayment = null;

    #[ORM\Column(nullable: true)]
    private ?int $warrantyDeposit = null;

    #[ORM\Column(nullable: true)]
    private ?int $rentChargeHonoraire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceAnnualRentGlobal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceAnnualChargeRentGlobal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceAnnualRentMeter = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceAnnualChargeRentMeter = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceChargeRentMonthHt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceRentAnnualCc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceRentAnnualHt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceChargeRentAnnualHt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceRentAnnualMeterCc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceRentAnnualMeterHt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceChargeRentAnnualMeterHt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceSurfaceDivisible = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceSurfaceDivisibleMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $commerceSurfaceDivisibleMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $rentWallMonth = null;

    #[ORM\Column(nullable: true)]
    private ?bool $rentCC = null;

    #[ORM\Column(nullable: true)]
    private ?bool $rentHT = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $honoraire = null;

    #[ORM\Column(nullable: true)]
    private ?bool $commerceRentalAnnual = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $coordLong = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $coordLat = null;

    #[ORM\OneToMany(mappedBy: 'refProperty', targetEntity: Reco::class)]
    private Collection $recos;

    public function __construct()
    {
        $this->Galery = new ArrayCollection();
        $this->Customer = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->cadastre = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->recos = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug !
     * Utilisation de slugify pour transformer une chaine de caractères en slug
     */
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

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getAnnonce(): ?string
    {
        return $this->annonce;
    }

    public function setAnnonce(?string $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getPiece(): ?int
    {
        return $this->piece;
    }

    public function setPiece(?int $piece): self
    {
        $this->piece = $piece;

        return $this;
    }

    public function getRoom(): ?int
    {
        return $this->room;
    }

    public function setRoom(?int $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getSurfaceLand(): ?int
    {
        return $this->surfaceLand;
    }

    public function setSurfaceLand(?int $surfaceLand): self
    {
        $this->surfaceLand = $surfaceLand;

        return $this;
    }

    public function getSurfaceHome(): ?int
    {
        return $this->surfaceHome;
    }

    public function setSurfaceHome(int $surfaceHome): self
    {
        $this->surfaceHome = $surfaceHome;

        return $this;
    }

    public function getDpeAt(): ?\DateTimeInterface
    {
        return $this->dpeAt;
    }

    public function setDpeAt(?\DateTimeInterface $dpeAt): self
    {
        $this->dpeAt = $dpeAt;

        return $this;
    }

    public function getDiagDpe(): ?int
    {
        return $this->diagDpe;
    }

    public function setDiagDpe(int $diagDpe): self
    {
        $this->diagDpe = $diagDpe;

        return $this;
    }

    public function getDiagGes(): ?int
    {
        return $this->diagGes;
    }

    public function setDiagGes(int $diagGes): self
    {
        $this->diagGes = $diagGes;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
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

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getOptions(): ?Complement
    {
        return $this->options;
    }

    public function setOptions(?Complement $options): self
    {
        $this->options = $options;

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
        return $this->name;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomer(): Collection
    {
        return $this->Customer;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->Customer->contains($customer)) {
            $this->Customer[] = $customer;
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        $this->Customer->removeElement($customer);

        return $this;
    }

    public function getIsIncreating(): ?bool
    {
        return $this->isIncreating;
    }

    public function setIsIncreating(bool $isIncreating): self
    {
        $this->isIncreating = $isIncreating;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getReflastnumber(): ?int
    {
        return $this->reflastnumber;
    }

    public function setReflastnumber(int $reflastnumber): self
    {
        $this->reflastnumber = $reflastnumber;

        return $this;
    }

    public function getRefnumdate(): ?string
    {
        return $this->refnumdate;
    }

    public function setRefnumdate(string $refnumdate): self
    {
        $this->refnumdate = $refnumdate;

        return $this;
    }

    public function getRefMandat(): ?string
    {
        return $this->RefMandat;
    }

    public function setRefMandat(string $RefMandat): self
    {
        $this->RefMandat = $RefMandat;

        return $this;
    }

    public function getDpeEstimateEnergyDown(): ?int
    {
        return $this->dpeEstimateEnergyDown;
    }

    public function setDpeEstimateEnergyDown(?int $dpeEstimateEnergyDown): self
    {
        $this->dpeEstimateEnergyDown = $dpeEstimateEnergyDown;

        return $this;
    }

    public function getConstructionAt(): ?string
    {
        return $this->constructionAt;
    }

    public function setConstructionAt(?int $constructionAt): self
    {
        $this->constructionAt = $constructionAt;

        return $this;
    }

    public function getDpeEstimateEnergyUp(): ?int
    {
        return $this->dpeEstimateEnergyUp;
    }

    public function setDpeEstimateEnergyUp(?int $dpeEstimateEnergyUp): self
    {
        $this->dpeEstimateEnergyUp = $dpeEstimateEnergyUp;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setProperty($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getProperty() === $this) {
                $photo->setProperty(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getHonoraires(): ?string
    {
        return $this->honoraires;
    }

    public function setHonoraires(string $honoraires): self
    {
        $this->honoraires = $honoraires;

        return $this;
    }

    public function getPriceFai(): ?string
    {
        return $this->priceFai;
    }

    public function setPriceFai(?string $priceFai): self
    {
        $this->priceFai = $priceFai;

        return $this;
    }

    public function getEeaYear(): ?\DateTimeInterface
    {
        return $this->eeaYear;
    }

    public function setEeaYear(\DateTimeInterface $eeaYear): self
    {
        $this->eeaYear = $eeaYear;

        return $this;
    }

    public function getNumberAvenant(): ?int
    {
        return $this->numberAvenant;
    }

    public function setNumberAvenant(?int $numberAvenant): self
    {
        $this->numberAvenant = $numberAvenant;

        return $this;
    }

    public function getDateAvenant(): ?\DateTimeInterface
    {
        return $this->dateAvenant;
    }

    public function setDateAvenant(?\DateTimeInterface $dateAvenant): self
    {
        $this->dateAvenant = $dateAvenant;

        return $this;
    }

    public function isIsWithExclusivity(): ?bool
    {
        return $this->isWithExclusivity;
    }

    public function setIsWithExclusivity(bool $isWithExclusivity): self
    {
        $this->isWithExclusivity = $isWithExclusivity;

        return $this;
    }

    public function isIsWithoutExclusivity(): ?bool
    {
        return $this->isWithoutExclusivity;
    }

    public function setIsWithoutExclusivity(bool $isWithoutExclusivity): self
    {
        $this->isWithoutExclusivity = $isWithoutExclusivity;

        return $this;
    }

    public function isIsSemiExclusivity(): ?bool
    {
        return $this->isSemiExclusivity;
    }

    public function setIsSemiExclusivity(bool $isSemiExclusivity): self
    {
        $this->isSemiExclusivity = $isSemiExclusivity;

        return $this;
    }

    public function getPropertyDefinition(): ?PropertyDefinition
    {
        return $this->propertyDefinition;
    }

    public function setPropertyDefinition(?PropertyDefinition $propertyDefinition): self
    {
        $this->propertyDefinition = $propertyDefinition;

        return $this;
    }

    /**
     * @return Collection<int, Cadaster>
     */
    public function getCadastre(): Collection
    {
        return $this->cadastre;
    }

    public function addCadastre(Cadaster $cadastre): self
    {
        if (!$this->cadastre->contains($cadastre)) {
            $this->cadastre->add($cadastre);
            $cadastre->setProperty($this);
        }

        return $this;
    }

    public function removeCadastre(Cadaster $cadastre): self
    {
        if ($this->cadastre->removeElement($cadastre)) {
            // set the owning side to null (unless already changed)
            if ($cadastre->getProperty() === $this) {
                $cadastre->setProperty(null);
            }
        }

        return $this;
    }

    public function getProjet(): ?string
    {
        return $this->projet;
    }

    public function setProjet(string $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getSscategory(): ?PropertySscategory
    {
        return $this->sscategory;
    }

    public function setSscategory(?PropertySscategory $sscategory): self
    {
        $this->sscategory = $sscategory;

        return $this;
    }

    public function getDiagChoice(): ?string
    {
        return $this->diagChoice;
    }

    public function setDiagChoice(string $diagChoice): self
    {
        $this->diagChoice = $diagChoice;

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
            $this->contacts->add($contact);
            $contact->setProperty($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getProperty() === $this) {
                $contact->setProperty(null);
            }
        }

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getMandatAt(): ?\DateTimeInterface
    {
        return $this->mandatAt;
    }

    public function setMandatAt(\DateTimeInterface $mandatAt): self
    {
        $this->mandatAt = $mandatAt;

        return $this;
    }

    public function getDupMandat(): ?string
    {
        return $this->dupMandat;
    }

    public function setDupMandat(?string $dupMandat): self
    {
        $this->dupMandat = $dupMandat;

        return $this;
    }

    public function getFamily(): ?propertyFamily
    {
        return $this->family;
    }

    public function setFamily(?propertyFamily $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getRubric(): ?propertyRubric
    {
        return $this->rubric;
    }

    public function setRubric(?propertyRubric $rubric): self
    {
        $this->rubric = $rubric;

        return $this;
    }

    public function getRubricss(): ?propertyRubricss
    {
        return $this->rubricss;
    }

    public function setRubricss(?propertyRubricss $rubricss): self
    {
        $this->rubricss = $rubricss;

        return $this;
    }

    public function isIsNomandat(): ?bool
    {
        return $this->isNomandat;
    }

    public function setIsNomandat(bool $isNomandat): self
    {
        $this->isNomandat = $isNomandat;

        return $this;
    }

    public function getDateEndmandat(): ?\DateTimeInterface
    {
        return $this->dateEndmandat;
    }

    public function setDateEndmandat(?\DateTimeInterface $dateEndmandat): self
    {
        $this->dateEndmandat = $dateEndmandat;

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function getAnnonceSlug(): ?string
    {
        return $this->annonceSlug;
    }

    public function setAnnonceSlug(?string $annonceSlug): self
    {
        $this->annonceSlug = $annonceSlug;

        return $this;
    }

    public function getRent(): ?int
    {
        return $this->rent;
    }

    public function setRent(?int $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getRentCharge(): ?int
    {
        return $this->rentCharge;
    }

    public function setRentCharge(?int $rentCharge): self
    {
        $this->rentCharge = $rentCharge;

        return $this;
    }

    public function isIsTransaction(): ?bool
    {
        return $this->isTransaction;
    }

    public function setIsTransaction(bool $isTransaction): static
    {
        $this->isTransaction = $isTransaction;

        return $this;
    }

    public function getRentChargeModsPayment(): ?int
    {
        return $this->rentChargeModsPayment;
    }

    public function setRentChargeModsPayment(?int $rentChargeModsPayment): static
    {
        $this->rentChargeModsPayment = $rentChargeModsPayment;

        return $this;
    }

    public function getWarrantyDeposit(): ?int
    {
        return $this->warrantyDeposit;
    }

    public function setWarrantyDeposit(?int $warrantyDeposit): static
    {
        $this->warrantyDeposit = $warrantyDeposit;

        return $this;
    }

    public function getRentChargeHonoraire(): ?int
    {
        return $this->rentChargeHonoraire;
    }

    public function setRentChargeHonoraire(?int $rentChargeHonoraire): static
    {
        $this->rentChargeHonoraire = $rentChargeHonoraire;

        return $this;
    }

    public function getCommerceAnnualRentGlobal(): ?string
    {
        return $this->commerceAnnualRentGlobal;
    }

    public function setCommerceAnnualRentGlobal(?string $commerceAnnualRentGlobal): static
    {
        $this->commerceAnnualRentGlobal = $commerceAnnualRentGlobal;

        return $this;
    }

    public function getCommerceAnnualChargeRentGlobal(): ?string
    {
        return $this->commerceAnnualChargeRentGlobal;
    }

    public function setCommerceAnnualChargeRentGlobal(string $commerceAnnualChargeRentGlobal): static
    {
        $this->commerceAnnualChargeRentGlobal = $commerceAnnualChargeRentGlobal;

        return $this;
    }

    public function getCommerceAnnualRentMeter(): ?string
    {
        return $this->commerceAnnualRentMeter;
    }

    public function setCommerceAnnualRentMeter(?string $commerceAnnualRentMeter): static
    {
        $this->commerceAnnualRentMeter = $commerceAnnualRentMeter;

        return $this;
    }

    public function getCommerceAnnualChargeRentMeter(): ?string
    {
        return $this->commerceAnnualChargeRentMeter;
    }

    public function setCommerceAnnualChargeRentMeter(?string $commerceAnnualChargeRentMeter): static
    {
        $this->commerceAnnualChargeRentMeter = $commerceAnnualChargeRentMeter;

        return $this;
    }

    public function isCommerceChargeRentMonthHt(): ?bool
    {
        return $this->commerceChargeRentMonthHt;
    }

    public function setCommerceChargeRentMonthHt(?bool $commerceChargeRentMonthHt): static
    {
        $this->commerceChargeRentMonthHt = $commerceChargeRentMonthHt;

        return $this;
    }

    public function isCommerceRentAnnualCc(): ?bool
    {
        return $this->commerceRentAnnualCc;
    }

    public function setCommerceRentAnnualCc(?bool $commerceRentAnnualCc): static
    {
        $this->commerceRentAnnualCc = $commerceRentAnnualCc;

        return $this;
    }

    public function isCommerceRentAnnualHt(): ?bool
    {
        return $this->commerceRentAnnualHt;
    }

    public function setCommerceRentAnnualHt(?bool $commerceRentAnnualHt): static
    {
        $this->commerceRentAnnualHt = $commerceRentAnnualHt;

        return $this;
    }

    public function isCommerceChargeRentAnnualHt(): ?bool
    {
        return $this->commerceChargeRentAnnualHt;
    }

    public function setCommerceChargeRentAnnualHt(?bool $commerceChargeRentAnnualHt): static
    {
        $this->commerceChargeRentAnnualHt = $commerceChargeRentAnnualHt;

        return $this;
    }

    public function isCommerceRentAnnualMeterCc(): ?bool
    {
        return $this->commerceRentAnnualMeterCc;
    }

    public function setCommerceRentAnnualMeterCc(?bool $commerceRentAnnualMeterCc): static
    {
        $this->commerceRentAnnualMeterCc = $commerceRentAnnualMeterCc;

        return $this;
    }

    public function isCommerceRentAnnualMeterHt(): ?bool
    {
        return $this->commerceRentAnnualMeterHt;
    }

    public function setCommerceRentAnnualMeterHt(?bool $commerceRentAnnualMeterHt): static
    {
        $this->commerceRentAnnualMeterHt = $commerceRentAnnualMeterHt;

        return $this;
    }

    public function isCommerceChargeRentAnnualMeterHt(): ?bool
    {
        return $this->commerceChargeRentAnnualMeterHt;
    }

    public function setCommerceChargeRentAnnualMeterHt(?bool $commerceChargeRentAnnualMeterHt): static
    {
        $this->commerceChargeRentAnnualMeterHt = $commerceChargeRentAnnualMeterHt;

        return $this;
    }

    public function isCommerceSurfaceDivisible(): ?bool
    {
        return $this->commerceSurfaceDivisible;
    }

    public function setCommerceSurfaceDivisible(?bool $commerceSurfaceDivisible): static
    {
        $this->commerceSurfaceDivisible = $commerceSurfaceDivisible;

        return $this;
    }

    public function getCommerceSurfaceDivisibleMin(): ?string
    {
        return $this->commerceSurfaceDivisibleMin;
    }

    public function setCommerceSurfaceDivisibleMin(?string $commerceSurfaceDivisibleMin): static
    {
        $this->commerceSurfaceDivisibleMin = $commerceSurfaceDivisibleMin;

        return $this;
    }

    public function getCommerceSurfaceDivisibleMax(): ?string
    {
        return $this->commerceSurfaceDivisibleMax;
    }

    public function setCommerceSurfaceDivisibleMax(?string $commerceSurfaceDivisibleMax): static
    {
        $this->commerceSurfaceDivisibleMax = $commerceSurfaceDivisibleMax;

        return $this;
    }

    public function getRentWallMonth(): ?string
    {
        return $this->rentWallMonth;
    }

    public function setRentWallMonth(?string $rentWallMonth): static
    {
        $this->rentWallMonth = $rentWallMonth;

        return $this;
    }

    public function isRentCC(): ?bool
    {
        return $this->rentCC;
    }

    public function setRentCC(?bool $rentCC): static
    {
        $this->rentCC = $rentCC;

        return $this;
    }

    public function isRentHT(): ?bool
    {
        return $this->rentHT;
    }

    public function setRentHT(?bool $rentHT): static
    {
        $this->rentHT = $rentHT;

        return $this;
    }

    public function getHonoraire(): ?string
    {
        return $this->honoraire;
    }

    public function setHonoraire(?string $honoraire): static
    {
        $this->honoraire = $honoraire;

        return $this;
    }

    public function isCommerceRentalAnnual(): ?bool
    {
        return $this->commerceRentalAnnual;
    }

    public function setCommerceRentalAnnual(?bool $commerceRentalAnnual): static
    {
        $this->commerceRentalAnnual = $commerceRentalAnnual;

        return $this;
    }

    public function getCoordLong(): ?string
    {
        return $this->coordLong;
    }

    public function setCoordLong(?string $coordLong): static
    {
        $this->coordLong = $coordLong;

        return $this;
    }

    public function getCoordLat(): ?string
    {
        return $this->coordLat;
    }

    public function setCoordLat(?string $coordLat): static
    {
        $this->coordLat = $coordLat;

        return $this;
    }

    /**
     * @return Collection<int, Reco>
     */
    public function getRecos(): Collection
    {
        return $this->recos;
    }

    public function addReco(Reco $reco): static
    {
        if (!$this->recos->contains($reco)) {
            $this->recos->add($reco);
            $reco->setRefProperty($this);
        }

        return $this;
    }

    public function removeReco(Reco $reco): static
    {
        if ($this->recos->removeElement($reco)) {
            // set the owning side to null (unless already changed)
            if ($reco->getRefProperty() === $this) {
                $reco->setRefProperty(null);
            }
        }

        return $this;
    }
}
