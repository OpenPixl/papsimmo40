<?php

namespace App\Entity\Gestapp;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Gestapp\Reco\AddReco;
use App\Controller\Api\Gestapp\Reco\AddRecoByIdCollaborator;
use App\Controller\Api\Gestapp\Reco\getRecoPrescriberEmail;
use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\StatutReco;
use App\Repository\Gestapp\RecoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecoRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
            uriTemplate: '/prescripteur/{email}/recommandations',
            //requirements: ['id' => '\d+'],
            uriVariables: [
                'email' => new Link(fromClass: Employed::class, fromProperty: 'email')
            ],
            //controller: getRecoPrescriberEmail::class,
            openapiContext: [
                'summary' => "Obtenir les recommandations par l'email du prescripteur.",
                'description' => "Obtenir les recommandations par l'email du prescripteur.",
            ],
            //normalizationContext: ['groups' => 'reco:list']
        ),
        new GetCollection(
            openapiContext: [
                'summary' => "Obtenir toutes les recommandations de l'API.",
                'description' => "Obtenir toutes les recommandations de l'API.",
            ],
        ),
        new GetCollection(
            uriTemplate: '/collaborateur/{refEmployed}/recommandations',
            uriVariables: [
                'refEmployed' => new Link(toProperty: 'refEmployed' , fromClass: Employed::class)
            ],
            //controller: addRecoByIdCollaborator::class,
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
#[ApiFilter(SearchFilter::class, properties: ['statutReco' => 'exact'])]
class Reco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reco:item', 'reco:write:post', 'reco:list'])]
    private ?Employed $refEmployed = null;

    #[ORM\Column(length: 80)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $announceFirstName = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $announceLastName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $announcePhone = null;

    #[ORM\Column(length: 100)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $announceEmail = null;

    #[ORM\Column(length: 80)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $customerFirstName = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $customerLastName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $customerPhone = null;

    #[ORM\Column(length: 100)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyComplement = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyZipcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyCity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyLong = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 5, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $propertyLat = null;

    #[ORM\ManyToOne]
    private ?StatutReco $statutReco = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\ManyToOne(inversedBy: 'recos')]
    #[Groups(['reco:item', 'reco:write:post'])]
    private ?Property $refProperty = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $StatusPrescriber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $typeProperty = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $typeReco = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['reco:item', 'employed:reco'])]
    private ?int $commission = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?int $kmArroundCity = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?int $budget = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reco:item', 'reco:write:post', 'employed:reco'])]
    private ?string $delayReco = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $OpenRecoAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $employedValidAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $recoPublishedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $onSaleAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $recoAbortedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $recoFinishedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paidCommissionAt = null;

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

    public function getStatutReco(): ?StatutReco
    {
        return $this->statutReco;
    }

    public function setStatutReco(?StatutReco $statutReco): static
    {
        $this->statutReco = $statutReco;

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

    public function getKmArroundCity(): ?int
    {
        return $this->kmArroundCity;
    }

    public function setKmArroundCity(?int $kmArroundCity): static
    {
        $this->kmArroundCity = $kmArroundCity;

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(?int $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getDelayReco(): ?string
    {
        return $this->delayReco;
    }

    public function setDelayReco(?string $delayReco): static
    {
        $this->delayReco = $delayReco;

        return $this;
    }

    public function getOpenRecoAt(): ?\DateTimeInterface
    {
        return $this->OpenRecoAt;
    }

    public function setOpenRecoAt(?\DateTimeInterface $OpenRecoAt): static
    {
        $this->OpenRecoAt = $OpenRecoAt;

        return $this;
    }

    public function getEmployedValidAt(): ?\DateTimeInterface
    {
        return $this->employedValidAt;
    }

    public function setEmployedValidAt(?\DateTimeInterface $employedValidAt): static
    {
        $this->employedValidAt = $employedValidAt;

        return $this;
    }

    public function getRecoPublishedAt(): ?\DateTimeInterface
    {
        return $this->recoPublishedAt;
    }

    public function setRecoPublishedAt(\DateTimeInterface $recoPublishedAt): static
    {
        $this->recoPublishedAt = $recoPublishedAt;

        return $this;
    }

    public function getOnSaleAt(): ?\DateTimeInterface
    {
        return $this->onSaleAt;
    }

    public function setOnSaleAt(?\DateTimeInterface $onSaleAt): static
    {
        $this->onSaleAt = $onSaleAt;

        return $this;
    }

    public function getRecoAbortedAt(): ?\DateTimeInterface
    {
        return $this->recoAbortedAt;
    }

    public function setRecoAbortedAt(?\DateTimeInterface $recoAbortedAt): static
    {
        $this->recoAbortedAt = $recoAbortedAt;

        return $this;
    }

    public function getRecoFinishedAt(): ?\DateTimeInterface
    {
        return $this->recoFinishedAt;
    }

    public function setRecoFinishedAt(\DateTimeInterface $recoFinishedAt): static
    {
        $this->recoFinishedAt = $recoFinishedAt;

        return $this;
    }

    public function getPaidCommissionAt(): ?\DateTimeInterface
    {
        return $this->paidCommissionAt;
    }

    public function setPaidCommissionAt(?\DateTimeInterface $paidCommissionAt): static
    {
        $this->paidCommissionAt = $paidCommissionAt;

        return $this;
    }
}
