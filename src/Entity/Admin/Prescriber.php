<?php

namespace App\Entity\Admin;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Gestapp\Prescriber\addPrescriber;
use App\Repository\Admin\PrescriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrescriberRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    shortName: 'Prescripteur',
    operations: [
        new Get(
            openapiContext: [
                'summary' => "Récupère la fiche d'un prescripteur.",
                'description' => "Récupère la fiche d'un prescripteur.",
            ],
            normalizationContext: ['groups' => 'prescripteur:item']
        ),
        new GetCollection(
            openapiContext: [
                'summary' => "Récupère les fiches des prescripteurs.",
                'description' => "Récupère les fiches des prescripteurs.",
            ],
            normalizationContext: ['groups' => 'prescripteur:list']
        ),
        new Post(
            uriTemplate: '/prescripteur',
            controller: addPrescriber::class,
            openapiContext: [
                'summary' => "Créer le compte pour le prescripteur.",
                'description' => "Créer le compte pour le prescripteur.",
            ],
            normalizationContext: ['groups' => 'prescripteur:write:post'],
            write: false
        ),
        new Patch(
            openapiContext: [
                'summary' => "Mettre à jour le compte pour le prescripteur.",
                'description' => "Mettre à jour le compte pour le prescripteur.",
            ],
            normalizationContext: ['groups' => 'prescripteur:write:patch']
        ),
        new Delete()
    ]
)]
class Prescriber implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $avatarName = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?int $avatarSize = null;

    #[ORM\Column(length: 14)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $home = null;

    #[ORM\Column(length: 14)]
    #[Groups(['prescripteur:list', 'prescripteur:item', 'prescripteur:write:post', 'prescripteur:write:patch'])]
    private ?string $gsm = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt = null;

    #[ORM\Column(length: 27, nullable: true)]
    private ?string $iban = null;

    #[ORM\ManyToOne(inversedBy: 'prescribers')]
    private ?Employed $refEmployed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): static
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    public function setAvatarSize(?int $avatarSize): static
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }

    public function getHome(): ?string
    {
        return $this->home;
    }

    public function setHome(string $home): static
    {
        $this->home = $home;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(string $gsm): static
    {
        $this->gsm = $gsm;

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
    public function setUpdatedAt(): sELF
    {
        $this->updatedAt = new \DateTime('now');

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getRefEmployed(): ?employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?employed $refEmployed): static
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }
}
