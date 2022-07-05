<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Employed;
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

    #[ORM\Column(type: 'boolean')]
    private $isHome = false;

    #[ORM\Column(type: 'boolean')]
    private $isApartment = false;

    #[ORM\Column(type: 'boolean')]
    private $isLand = false;

    #[ORM\Column(type: 'boolean')]
    private $isOther = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $otherDescription;

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

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $notaryEstimate;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $applicantEstimate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $cadasterZone;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cadasterNum;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cadasterSurface;

    #[ORM\OneToOne(targetEntity: Complement::class, cascade: ['persist', 'remove'])]
    private $options;

    #[Vich\UploadableField(mapping: 'property_image', fileNameProperty:"imageName", size:"imageSize")]
    #[Ignore]
    private $imageFile;

    #[ORM\Column(type: 'string', nullable: true)]
    private $imageName;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $imageSize;

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

    public function __construct()
    {
        $this->Galery = new ArrayCollection();
        $this->Customer = new ArrayCollection();
        $this->photos = new ArrayCollection();
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

    public function getIsHome(): ?bool
    {
        return $this->isHome;
    }

    public function setIsHome(bool $isHome): self
    {
        $this->isHome = $isHome;

        return $this;
    }

    public function getIsApartment(): ?bool
    {
        return $this->isApartment;
    }

    public function setIsApartment(bool $isApartment): self
    {
        $this->isApartment = $isApartment;

        return $this;
    }

    public function getIsLand(): ?bool
    {
        return $this->isLand;
    }

    public function setIsLand(bool $isLand): self
    {
        $this->isLand = $isLand;

        return $this;
    }

    public function getIsOther(): ?bool
    {
        return $this->isOther;
    }

    public function setIsOther(bool $isOther): self
    {
        $this->isOther = $isOther;

        return $this;
    }

    public function getOtherDescription(): ?string
    {
        return $this->otherDescription;
    }

    public function setOtherDescription(string $otherDescription): self
    {
        $this->otherDescription = $otherDescription;

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

    public function getNotaryEstimate(): ?string
    {
        return $this->notaryEstimate;
    }

    public function setNotaryEstimate(?string $notaryEstimate): self
    {
        $this->notaryEstimate = $notaryEstimate;

        return $this;
    }

    public function getApplicantEstimate(): ?string
    {
        return $this->applicantEstimate;
    }

    public function setApplicantEstimate(?string $applicantEstimate): self
    {
        $this->applicantEstimate = $applicantEstimate;

        return $this;
    }

    public function getCadasterZone(): ?string
    {
        return $this->cadasterZone;
    }

    public function setCadasterZone(?string $cadasterZone): self
    {
        $this->cadasterZone = $cadasterZone;

        return $this;
    }

    public function getCadasterNum(): ?int
    {
        return $this->cadasterNum;
    }

    public function setCadasterNum(?int $cadasterNum): self
    {
        $this->cadasterNum = $cadasterNum;

        return $this;
    }

    public function getCadasterSurface(): ?int
    {
        return $this->cadasterSurface;
    }

    public function setCadasterSurface(?int $cadasterSurface): self
    {
        $this->cadasterSurface = $cadasterSurface;

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
    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
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

}
