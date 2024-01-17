<?php

namespace App\Entity\Gestapp;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\Gestapp\Reco\AddReco;
use App\Entity\Admin\Employed;
use App\Repository\Gestapp\RecoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecoRepository::class)]
#[ApiResource(
    shortName: 'Recommandation',
    operations: [
        new Get(
            uriTemplate: '/recommandation/{id}',
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => "Obtenir une recommandation.",
                'description' => "Obtenir une recommandation.",
            ],
            normalizationContext: ['groups' => 'reco:item']
        ),
        new GetCollection(
            openapiContext: [
                'summary' => "Obtenir toutes les recomandations de l'API.",
                'description' => "Obtenir toutes les recomandations de l'API.",
            ],
        ),
        new GetCollection(
            uriTemplate: '/collaborateur/{id}/recommandations',
            uriVariables: [
                'id' => new Link(fromProperty: 'Customer' , fromClass: Employed::class)
            ],
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => "Obtenir uniquement les recommandations du mandataire.",
                'description' => "Obtenir uniquement les recommandations du mandataire.",
            ],
        ),
        new Post(
            uriTemplate: '/recommandation',
            controller: AddReco::class,
            openapiContext: [
                'summary' => "Créer une nouvelle recommandation pour le mandataire",
                'description' => "Créer une nouvelle recommandation pour le mandataire",
            ],
            normalizationContext: ['groups' => ['reco:write:post']]
        ),
        new Patch(
            uriTemplate: '/recommandation/{id}/update',
            openapiContext: [
                'summary' => "Modification d'une recommandation.",
                'description' => "Modification d'une recommandation.",
            ],
            normalizationContext: ['groups' => ['reco:write:post']]
        ),
        new Delete()
    ],
    paginationEnabled: false
)]
class Reco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?Employed $refEmployed = null;

    #[ORM\Column(length: 80)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $announceFirstName = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $announceLastName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $announcePhone = null;

    #[ORM\Column(length: 100)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $announceEmail = null;

    #[ORM\Column(length: 80)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $customerFirstName = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $customerLastName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $customerPhone = null;

    #[ORM\Column(length: 100)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyComplement = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyZipcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyCity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyLong = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $propertyLat = null;

    #[ORM\Column(length: 25)]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?string $statutReco = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\ManyToOne(inversedBy: 'recos')]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?Property $refProperty = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $StatusPrescriber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeProperty = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeReco = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['reco:item'])]
    private ?int $commission = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefEmployed(): ?Employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?Employed $refEmployed): static
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }

    public function getAnnounceFirstName(): ?string
    {
        return $this->announceFirstName;
    }

    public function setAnnounceFirstName(string $announceFirstName): static
    {
        $this->announceFirstName = $announceFirstName;

        return $this;
    }

    public function getAnnounceLastName(): ?string
    {
        return $this->announceLastName;
    }

    public function setAnnounceLastName(?string $announceLastName): static
    {
        $this->announceLastName = $announceLastName;

        return $this;
    }

    public function getAnnouncePhone(): ?string
    {
        return $this->announcePhone;
    }

    public function setAnnouncePhone(string $announcePhone): static
    {
        $this->announcePhone = $announcePhone;

        return $this;
    }

    public function getAnnounceEmail(): ?string
    {
        return $this->announceEmail;
    }

    public function setAnnounceEmail(string $announceEmail): static
    {
        $this->announceEmail = $announceEmail;

        return $this;
    }

    public function getCustomerFirstName(): ?string
    {
        return $this->customerFirstName;
    }

    public function setCustomerFirstName(string $customerFirstName): static
    {
        $this->customerFirstName = $customerFirstName;

        return $this;
    }

    public function getCustomerLastName(): ?string
    {
        return $this->customerLastName;
    }

    public function setCustomerLastName(?string $customerLastName): static
    {
        $this->customerLastName = $customerLastName;

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): static
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getPropertyAddress(): ?string
    {
        return $this->propertyAddress;
    }

    public function setPropertyAddress(string $propertyAddress): static
    {
        $this->propertyAddress = $propertyAddress;

        return $this;
    }

    public function getPropertyComplement(): ?string
    {
        return $this->propertyComplement;
    }

    public function setPropertyComplement(?string $propertyComplement): static
    {
        $this->propertyComplement = $propertyComplement;

        return $this;
    }

    public function getPropertyZipcode(): ?string
    {
        return $this->propertyZipcode;
    }

    public function setPropertyZipcode(?string $propertyZipcode): static
    {
        $this->propertyZipcode = $propertyZipcode;

        return $this;
    }

    public function getPropertyCity(): ?string
    {
        return $this->propertyCity;
    }

    public function setPropertyCity(?string $propertyCity): static
    {
        $this->propertyCity = $propertyCity;

        return $this;
    }

    public function getPropertyLong(): ?string
    {
        return $this->propertyLong;
    }

    public function setPropertyLong(?string $propertyLong): static
    {
        $this->propertyLong = $propertyLong;

        return $this;
    }

    public function getPropertyLat(): ?string
    {
        return $this->propertyLat;
    }

    public function setPropertyLat(?string $propertyLat): static
    {
        $this->propertyLat = $propertyLat;

        return $this;
    }

    public function getStatutReco(): ?string
    {
        return $this->statutReco;
    }

    public function setStatutReco(string $statutReco): static
    {
        $this->statutReco = $statutReco;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getRefProperty(): ?Property
    {
        return $this->refProperty;
    }

    public function setRefProperty(?Property $refProperty): static
    {
        $this->refProperty = $refProperty;

        return $this;
    }

    public function getStatusPrescriber(): ?string
    {
        return $this->StatusPrescriber;
    }

    public function setStatusPrescriber(string $StatusPrescriber): static
    {
        $this->StatusPrescriber = $StatusPrescriber;

        return $this;
    }

    public function getTypeProperty(): ?string
    {
        return $this->typeProperty;
    }

    public function setTypeProperty(?string $typeProperty): static
    {
        $this->typeProperty = $typeProperty;

        return $this;
    }

    public function getTypeReco(): ?string
    {
        return $this->typeReco;
    }

    public function setTypeReco(?string $typeReco): static
    {
        $this->typeReco = $typeReco;

        return $this;
    }

    public function getCommission(): ?int
    {
        return $this->commission;
    }

    public function setCommission(?int $commission): static
    {
        $this->commission = $commission;

        return $this;
    }
}
