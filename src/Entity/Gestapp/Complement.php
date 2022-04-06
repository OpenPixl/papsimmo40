<?php

namespace App\Entity\Gestapp;

use App\Entity\Gestapp\choice\ApartmentType;
use App\Entity\Gestapp\choice\BuildingEquipment;
use App\Entity\Gestapp\choice\Denomination;
use App\Entity\Gestapp\choice\HouseType;
use App\Entity\Gestapp\choice\LandType;
use App\Entity\Gestapp\choice\OtherOption;
use App\Entity\Gestapp\choice\TradeType;
use App\Entity\Gestapp\choice\houseEquipment;
use App\Repository\Gestapp\ComplementRepository;
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

    #[ORM\Column(type: 'string', length: 100)]
    private $disponibility;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $disponibilityAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $constructionAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $propertyTax;

    #[ORM\ManyToOne(targetEntity: HouseType::class)]
    private $houseType;

    #[ORM\ManyToOne(targetEntity: ApartmentType::class)]
    private $apartmentType;

    #[ORM\ManyToOne(targetEntity: LandType::class)]
    private $landType;

    #[ORM\ManyToOne(targetEntity: TradeType::class)]
    private $tradeType;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $orientation;

    #[ORM\ManyToOne(targetEntity: BuildingEquipment::class)]
    private $buildingEquipment;

    #[ORM\ManyToOne(targetEntity: houseEquipment::class)]
    private $houseEquipment;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $houseState;

    #[ORM\ManyToOne(targetEntity: OtherOption::class)]
    private $otherOption;

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
    private $isFurnished;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $energy;

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

    public function getDisponibilityAt(): ?\DateTimeImmutable
    {
        return $this->disponibilityAt;
    }

    public function setDisponibilityAt(?\DateTimeImmutable $disponibilityAt): self
    {
        $this->disponibilityAt = $disponibilityAt;

        return $this;
    }

    public function getConstructionAt(): ?\DateTimeImmutable
    {
        return $this->constructionAt;
    }

    public function setConstructionAt(\DateTimeImmutable $constructionAt): self
    {
        $this->constructionAt = $constructionAt;

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

    public function getHouseType(): ?HouseType
    {
        return $this->houseType;
    }

    public function setHouseType(?HouseType $houseType): self
    {
        $this->houseType = $houseType;

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

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getBuildingEquipment(): ?BuildingEquipment
    {
        return $this->buildingEquipment;
    }

    public function setBuildingEquipment(?BuildingEquipment $buildingEquipment): self
    {
        $this->buildingEquipment = $buildingEquipment;

        return $this;
    }

    public function getHouseEquipment(): ?houseEquipment
    {
        return $this->houseEquipment;
    }

    public function setHouseEquipment(?houseEquipment $houseEquipment): self
    {
        $this->houseEquipment = $houseEquipment;

        return $this;
    }

    public function getHouseState(): ?string
    {
        return $this->houseState;
    }

    public function setHouseState(?string $houseState): self
    {
        $this->houseState = $houseState;

        return $this;
    }

    public function getOtherOption(): ?OtherOption
    {
        return $this->otherOption;
    }

    public function setOtherOption(?OtherOption $otherOption): self
    {
        $this->otherOption = $otherOption;

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

    public function getEnergy(): ?string
    {
        return $this->energy;
    }

    public function setEnergy(?string $energy): self
    {
        $this->energy = $energy;

        return $this;
    }
}
