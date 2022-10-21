<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Repository\Gestapp\PropertyRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;


#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]

class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $ref;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'properties')]
    private $refEmployed;

    #[ORM\Column(type: 'text', nullable: true)]
    private $annonce;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $piece;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $room;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $surfaceLand;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $surfaceHome;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dpeAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $diagDpe;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $diagGpe;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $complement;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $zipcode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\OneToOne(targetEntity: Complement::class, cascade: ['persist', 'remove'])]
    private $options;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Customer::class, inversedBy: 'properties')]
    private $Customer;

    #[ORM\Column(type: 'boolean')]
    private $isIncreating = true;

    #[ORM\OneToOne(targetEntity: Publication::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $publication;

    #[ORM\Column(type: 'integer')]
    private $reflastnumber;

    #[ORM\Column(type: 'string', length: 7)]
    private $refnumdate;

    #[ORM\Column(type: 'string', length: 255)]
    private $RefMandat;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $dpeEstimateEnergyDown;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $dpeEstimateEnergyUp;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $constructionAt;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Photo::class)]
    private $photos;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $otherDescription;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0', nullable: true)]
    private $Price;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0')]
    private $honoraires;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0', nullable: true)]
    private $priceFai;

    #[ORM\Column(type: 'date', nullable: true)]
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

    public function __construct()
    {
        $this->Galery = new ArrayCollection();
        $this->Customer = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->cadastre = new ArrayCollection();
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

    public function getSurfaceLand(): ?string
    {
        return $this->surfaceLand;
    }

    public function setSurfaceLand(?string $surfaceLand): self
    {
        $this->surfaceLand = $surfaceLand;

        return $this;
    }

    public function getSurfaceHome(): ?string
    {
        return $this->surfaceHome;
    }

    public function setSurfaceHome(string $surfaceHome): self
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

    public function getDiagGpe(): ?string
    {
        return $this->diagGpe;
    }

    public function setDiagGpe(string $diagGpe): self
    {
        $this->diagGpe = $diagGpe;

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

    public function getConstructionAt(): ?\DateTimeInterface
    {
        return $this->constructionAt;
    }

    public function setConstructionAt(?\DateTimeInterface $constructionAt): self
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

    public function getOtherDescription(): ?string
    {
        return $this->otherDescription;
    }

    public function setOtherDescription(?string $otherDescription): self
    {
        $this->otherDescription = $otherDescription;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->Price;
    }

    public function setPrice(?string $Price): self
    {
        $this->Price = $Price;

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

    public function setEeaYear(?\DateTimeInterface $eeaYear): self
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
}
