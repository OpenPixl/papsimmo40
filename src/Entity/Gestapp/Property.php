<?php

namespace App\Entity\Gestapp;

use App\Entity\Admin\Employed;
use App\Repository\Gestapp\PropertyRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\HasLifecycleCallbacks]
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cadasterCariez;

    #[ORM\OneToOne(targetEntity: Complement::class, cascade: ['persist', 'remove'])]
    private $options;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Media::class)]
    private $Media;

    #[ORM\ManyToMany(targetEntity: Customer::class, inversedBy: 'properties')]
    private $Customer;

    #[ORM\Column(type: 'boolean')]
    private $isIncreating = true;

    #[ORM\OneToOne(targetEntity: Publication::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $publication;

    public function __construct()
    {
        $this->Media = new ArrayCollection();
        $this->Customer = new ArrayCollection();
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

    public function getCadasterCariez(): ?int
    {
        return $this->cadasterCariez;
    }

    public function setCadasterCariez(?int $cadasterCariez): self
    {
        $this->cadasterCariez = $cadasterCariez;

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
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->Media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->Media->contains($medium)) {
            $this->Media[] = $medium;
            $medium->setProperty($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->Media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getProperty() === $this) {
                $medium->setProperty(null);
            }
        }

        return $this;
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
}
