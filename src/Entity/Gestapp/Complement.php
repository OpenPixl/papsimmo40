<?php

namespace App\Entity\Gestapp;

use App\Entity\Gestapp\choice\ApartmentType;
use App\Entity\Gestapp\choice\BuildingEquipment;
use App\Entity\Gestapp\choice\Denomination;
use App\Entity\Gestapp\choice\HouseType;
use App\Entity\Gestapp\choice\LandType;
use App\Entity\Gestapp\choice\OtherOption;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Entity\Gestapp\choice\PropertyEquipement;
use App\Entity\Gestapp\choice\PropertyOrientation;
use App\Entity\Gestapp\choice\PropertyState;
use App\Entity\Gestapp\choice\PropertyTypology;
use App\Entity\Gestapp\choice\TradeType;
use App\Entity\Gestapp\choice\HouseEquipment;
use App\Repository\Gestapp\ComplementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplementRepository::class)]
class Complement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $banner;

    #[ORM\ManyToOne(targetEntity: Denomination::class)]
    private $denomination;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $location;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $disponibility;

    #[ORM\Column(type: 'date', nullable: true)]
    private $disponibilityAt;

    #[ORM\ManyToOne(targetEntity: ApartmentType::class)]
    private $apartmentType;

    #[ORM\ManyToOne(targetEntity: LandType::class)]
    private $landType;

    #[ORM\ManyToOne(targetEntity: TradeType::class)]
    private $tradeType;

    #[ORM\ManyToOne(targetEntity: BuildingEquipment::class)]
    private $BuildingEquipment;

    #[ORM\ManyToOne(targetEntity: HouseEquipment::class)]
    private $HouseEquipment;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $level;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $jointness;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $washroom;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $bathroom;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $wc;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $terrace;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $balcony;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $sanitation;

    #[ORM\Column(type: 'boolean')]
    private $isFurnished = false;

    #[ORM\ManyToOne(targetEntity: PropertyState::class)]
    private $propertyState;

    #[ORM\ManyToOne(targetEntity: PropertyEnergy::class)]
    private $propertyEnergy;

    #[ORM\ManyToMany(targetEntity: PropertyEquipement::class, inversedBy: 'complements')]
    private $propertyEquipment;

    #[ORM\ManyToOne(targetEntity: PropertyTypology::class)]
    private $propertyTypology;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $propertyTax;

    #[ORM\ManyToOne(targetEntity: PropertyOrientation::class)]
    private $propertyOrientation;

    #[ORM\ManyToMany(targetEntity: OtherOption::class, inversedBy: 'complements')]
    private $propertyOtheroption;

    public function __construct()
    {
        $this->propertyEquipment = new ArrayCollection();
        $this->propertyOtheroption = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function getDenomination(): ?Denomination
    {
        return $this->denomination;
    }

    public function setDenomination(?Denomination $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDisponibility(): ?string
    {
        return $this->disponibility;
    }

    public function setDisponibility(string $disponibility): self
    {
        $this->disponibility = $disponibility;

        return $this;
    }

    public function getDisponibilityAt(): ?\DateTimeInterface
    {
        return $this->disponibilityAt;
    }

    public function setDisponibilityAt(?\DateTimeInterface $disponibilityAt): self
    {
        $this->disponibilityAt = $disponibilityAt;

        return $this;
    }

    public function getApartmentType(): ?ApartmentType
    {
        return $this->apartmentType;
    }

    public function setApartmentType(?ApartmentType $apartmentType): self
    {
        $this->apartmentType = $apartmentType;

        return $this;
    }

    public function getLandType(): ?LandType
    {
        return $this->landType;
    }

    public function setLandType(?LandType $landType): self
    {
        $this->landType = $landType;

        return $this;
    }

    public function getTradeType(): ?TradeType
    {
        return $this->tradeType;
    }

    public function setTradeType(?TradeType $tradeType): self
    {
        $this->tradeType = $tradeType;

        return $this;
    }

    public function getBuildingEquipment(): ?BuildingEquipment
    {
        return $this->BuildingEquipment;
    }

    public function setBuildingEquipment(?BuildingEquipment $BuildingEquipment): self
    {
        $this->BuildingEquipment = $BuildingEquipment;

        return $this;
    }

    public function getHouseEquipment(): ?HouseEquipment
    {
        return $this->HouseEquipment;
    }

    public function setHouseEquipment(?HouseEquipment $HouseEquipment): self
    {
        $this->HouseEquipment = $HouseEquipment;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getJointness(): ?int
    {
        return $this->jointness;
    }

    public function setJointness(?int $jointness): self
    {
        $this->jointness = $jointness;

        return $this;
    }

    public function getWashroom(): ?int
    {
        return $this->washroom;
    }

    public function setWashroom(?int $washroom): self
    {
        $this->washroom = $washroom;

        return $this;
    }

    public function getBathroom(): ?int
    {
        return $this->bathroom;
    }

    public function setBathroom(?int $bathroom): self
    {
        $this->bathroom = $bathroom;

        return $this;
    }

    public function getWc(): ?int
    {
        return $this->wc;
    }

    public function setWc(?int $wc): self
    {
        $this->wc = $wc;

        return $this;
    }

    public function getTerrace(): ?int
    {
        return $this->terrace;
    }

    public function setTerrace(?int $terrace): self
    {
        $this->terrace = $terrace;

        return $this;
    }

    public function getBalcony(): ?int
    {
        return $this->balcony;
    }

    public function setBalcony(?int $balcony): self
    {
        $this->balcony = $balcony;

        return $this;
    }

    public function getSanitation(): ?string
    {
        return $this->sanitation;
    }

    public function setSanitation(?string $sanitation): self
    {
        $this->sanitation = $sanitation;

        return $this;
    }

    public function getIsFurnished(): ?bool
    {
        return $this->isFurnished;
    }

    public function setIsFurnished(bool $isFurnished): self
    {
        $this->isFurnished = $isFurnished;

        return $this;
    }

    public function getPropertyState(): ?PropertyState
    {
        return $this->propertyState;
    }

    public function setPropertyState(?PropertyState $propertyState): self
    {
        $this->propertyState = $propertyState;

        return $this;
    }

    public function getPropertyEnergy(): ?PropertyEnergy
    {
        return $this->propertyEnergy;
    }

    public function setPropertyEnergy(?PropertyEnergy $propertyEnergy): self
    {
        $this->propertyEnergy = $propertyEnergy;

        return $this;
    }

    /**
     * @return Collection<int, PropertyEquipement>
     */
    public function getPropertyEquipment(): Collection
    {
        return $this->propertyEquipment;
    }

    public function addPropertyEquipment(PropertyEquipement $propertyEquipment): self
    {
        if (!$this->propertyEquipment->contains($propertyEquipment)) {
            $this->propertyEquipment[] = $propertyEquipment;
        }

        return $this;
    }

    public function removePropertyEquipment(PropertyEquipement $propertyEquipment): self
    {
        $this->propertyEquipment->removeElement($propertyEquipment);

        return $this;
    }

    public function getPropertyTypology(): ?PropertyTypology
    {
        return $this->propertyTypology;
    }

    public function setPropertyTypology(?PropertyTypology $propertyTypology): self
    {
        $this->propertyTypology = $propertyTypology;

        return $this;
    }

    public function getPropertyTax(): ?string
    {
        return $this->propertyTax;
    }

    public function setPropertyTax(?string $propertyTax): self
    {
        $this->propertyTax = $propertyTax;

        return $this;
    }

    public function getPropertyOrientation(): ?PropertyOrientation
    {
        return $this->propertyOrientation;
    }

    public function setPropertyOrientation(?PropertyOrientation $propertyOrientation): self
    {
        $this->propertyOrientation = $propertyOrientation;

        return $this;
    }

    /**
     * @return Collection<int, OtherOption>
     */
    public function getPropertyOtheroption(): Collection
    {
        return $this->propertyOtheroption;
    }

    public function addPropertyOtheroption(OtherOption $propertyOtheroption): self
    {
        if (!$this->propertyOtheroption->contains($propertyOtheroption)) {
            $this->propertyOtheroption[] = $propertyOtheroption;
        }

        return $this;
    }

    public function removePropertyOtheroption(OtherOption $propertyOtheroption): self
    {
        $this->propertyOtheroption->removeElement($propertyOtheroption);

        return $this;
    }
}
