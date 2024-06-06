<?php

namespace App\Entity\Admin;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Admin\Employed\AddEmployed;
use App\Controller\Api\Admin\Employed\AddPrescriber;
use App\Controller\Api\Admin\Employed\GetTokenEmailPrescripteur;
use App\Controller\Api\Admin\Employed\GetTokenEmployed;
use App\Controller\Api\Admin\Employed\updatePrescriberpassword;
use App\Entity\Admin\Employed\Account;
use App\Entity\Cart\Cart;
use App\Entity\Cart\Purchase;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Project;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Reco;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\AddCollTransac;
use App\Entity\Webapp\Articles;
use App\Entity\Webapp\Page;
use App\Entity\Webapp\Section;
use App\Repository\Admin\EmployedRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployedRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'un compte avec la même adresse mail existe déjà')]
#[ApiResource(
    shortName: 'Collaborateur',
    operations: [
        new Get(
            uriTemplate: '/collaborateur/{id}',
            openapiContext: [
                'summary' => "Récupérer les information  d'un collaborateur.",
                'description' => "Récupérer les information  d'un collaborateur.",
            ],
            normalizationContext: ['groups' => 'employed:item']),
        new Get(
            uriTemplate: '/prescripteur/{email}',
            uriVariables: [
                'email' => 'email'
            ],
            openapiContext: [
                'summary' => "Récupérer les information  d'un collaborateur par son email.",
                'description' => "Récupérer les information  d'un collaborateur par son email.",
            ],
            normalizationContext: ['groups' => 'employed:item']),
        new Get(
            uriTemplate: '/mandataire/{numCollaborator}',
            uriVariables: [
                'numCollaborator' => 'numCollaborator'
            ],
            requirements: ['numCollaborator' => '\d+'],
            openapiContext: [
                'summary' => "Récupérer les information  d'un collaborateur par son numéro",
                'description' => "Récupérer les information  d'un collaborateur par son numéro",
            ],
            normalizationContext: ['groups' => 'employed:item'],
        ),
        new Get(
            uriTemplate: '/prescripteur/{email}/recommandations/',
            uriVariables: [
                'email' => 'email'
            ],
            //requirements: ['numCollaborator' => '\d+'],
            openapiContext: [
                'summary' => "Récupérer les information d'un collaborateur par son email",
                'description' => "Récupérer les information d'un collaborateur par son email",
            ],
            normalizationContext: ['groups' => 'employed:reco'],
        ),
        new GetCollection(
            openapiContext: [
                'summary' => "Récupérer la liste des collaborateurs.",
                'description' => "Récupérer la liste des collaborateurs.",
            ],
            normalizationContext: ['groups' => 'employed:list']
        ),
        new GetCollection(
            uriTemplate: '/search/collaborateur/',
            openapiContext: [
                'summary' => "Récupérer la liste des collaborateurs filtrées.",
                'description' => "Récupérer la liste des collaborateurs filtrées.",
            ],
            normalizationContext: ['groups' => 'employed:search']
        ),
        new Get(
            uriTemplate: '/authentication_token/{numCollaborator}/getToken',
            uriVariables: [
                'numCollaborator' => 'numCollaborator'
            ],
            requirements: ['numCollaborator' => '\d+'],
            controller: GetTokenEmployed::class,
            openapiContext: [
                'summary' => "Récupérer un token par l'identifiant du mandataire",
                'description' => "Récupérer un token par l'identifiant du mandataire",
            ],
            normalizationContext: ['groups' => 'employed:item'],
            name: 'getTokenByNumCollaborator'
        ),
        new Get(
            uriTemplate: '/authentication_token/prescripteur/{email}',
            uriVariables: [
                'email' => 'email'
            ],
            //requirements: ['email' => '\d+'],
            controller: GetTokenEmailPrescripteur::class,
            openapiContext: [
                'summary' => "Récupérer un token par l'email du prescripteur",
                'description' => "Récupérer un token par l'email du prescripteur",
            ],
            normalizationContext: ['groups' => 'employed:item'],
            name: 'getTokenByEmailPrescripteur'
        ),
        new Post(
            uriTemplate: '/collaborateur',
            controller: AddEmployed::class,
            openapiContext: [
                'summary' => "Ajoute un collaborateur",
                'description' => "Ajoute un collaborateur",
            ],
            normalizationContext: ['groups' => 'employed:write:post']
        ),
        new POST(
            uriTemplate: '/prescripteur',
            controller: AddPrescriber::class,
            openapiContext: [
                'summary' => "Ajoute un collaborateur",
                'description' => "Ajoute un collaborateur",
            ],
            normalizationContext: ['groups' => 'employed:write:post']
        ),
        new Patch(
            uriTemplate: '/collaborateur/{id}/update',
            openapiContext: [
                'summary' => "Mettre à jour les informations du collaborateur",
                'description' => "Mettre à jour les informations du collaborateur",
            ],
            normalizationContext: ['groups' => ['employed:write:patch']]
        ),
        new Patch(
            uriTemplate: '/prescripteur/{id}/update',
            openapiContext: [
                'summary' => "Mettre à jour les informations du prescripteur.",
                'description' => "Mettre à jour les informations du prescripteur.",
            ],
            normalizationContext: ['groups' => ['employed:write:patch']]
        ),
        new Patch(
            uriTemplate: '/prescripteur/{email}/update',
            uriVariables: [
                'email' => 'email'
            ],
            openapiContext: [
                'summary' => "Mettre à jour les informations du prescripteur.",
                'description' => "Mettre à jour les informations du prescripteur.",
            ],
            normalizationContext: ['groups' => ['employed:write:patch']]
        ),
        new Patch(
            uriTemplate: '/prescripteur/{email}/updatepassword',
            uriVariables: [
                'email' => 'email'
            ],
            controller: updatePrescriberpassword::class,
            openapiContext: [
                'summary' => "Mettre à jour les informations du prescripteur.",
                'description' => "Mettre à jour les informations du prescripteur.",
            ],
            normalizationContext: ['groups' => ['prescripteur:write:patch']]
        )

    ],
    paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['genre' => 'exact', 'firstName' => 'word_start'])]
class Employed implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['employed:item','reco:list','reco:item', 'reco:write:post', 'employed:reco'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['employed:list', 'employed:item', 'employed:write:post', 'employed:write:patch', 'employed:reco', 'prescriber:write:post', 'transaction:list', 'reco:list'])]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(('employed:list'))]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['prescriber:write:post', 'prescriber:write:patch'])]
    private $password;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['employed:list', 'employed:item', 'employed:write:post','employed:write:patch', 'employed:reco', 'employed:search', 'client:item', 'prescriber:write:post', 'reco:item','transaction:list'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['employed:list', 'employed:item', 'employed:write:post','employed:write:patch', 'employed:reco', 'employed:search', 'client:item', 'prescriber:write:post', 'reco:item','transaction:list'])]
    private $lastName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maidenName = null;

    #[ORM\Column(type: 'string', length: 80)]
    private $slug;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    #[Groups(['employed:list', 'employed:item', 'employed:write:post','employed:write:patch'])]
    private $sector;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['employed:list', 'employed:item'])]
    private $referent;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Customer::class)]
    private $Customer;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Property::class)]
    private $properties;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Project::class)]
    private $projects;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Articles::class)]
    private $articles;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Section::class)]
    private $sections;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Page::class)]
    private $pages;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['employed:list', 'employed:item'])]
    private $avatarName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['employed:list', 'employed:item'])]
    private $avatarSize = null;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $home;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $desk;

    #[ORM\Column(type: 'string', length: 14)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch', 'prescriber:write:post'])]
    private $gsm;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $fax;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $otherEmail;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $facebook;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $instagram;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private $linkedin;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'forEmployed', targetEntity: Contact::class)]
    private Collection $contacts;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private ?string $employedPrez = null;

    #[ORM\Column]
    private ?bool $isWebpublish = false;

    #[ORM\Column(length: 6, nullable: true)]
    #[Groups(['employed:list', 'employed:item'])]
    #[Assert\Length(
        min: 6,
        minMessage: 'Nous attendons 6 caractères, il en manque',
        max: 6,
        maxMessage: 'Nous attendons 6 caractères, il y en a trop',
    )]
    private ?string $numCollaborator = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch'])]
    private ?string $urlWeb = null;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Reco::class)]
    #[Groups(['employed:reco'])]
    private Collection $recos;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEmployed = null;

    #[ORM\Column]
    private ?bool $isSupprAvatar = false;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['employed:list', 'employed:item','employed:write:patch', 'employed:reco'])]
    private ?string $iban = null;

    #[ORM\Column]
    private ?bool $isGdpr = false;

    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Purchase::class)]
    private Collection $purchases;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['employed:list'])]
    private ?string $genre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ciFileName = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $ciFileext = null;

    #[ORM\Column(nullable: true)]
    private ?int $ciFilesize = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isSupprCi = false;

    #[ORM\OneToMany(mappedBy: 'refemployed', targetEntity: AddCollTransac::class, orphanRemoval: true)]
    private Collection $addCollTransacs;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employed:list', 'employed:item', 'employed:write:post', 'employed:write:patch', 'employed:reco', 'prescriber:write:post', 'transaction:list', 'reco:list'])]
    private ?string $civility = null;

    /**
     * @var Collection<int, Account>
     */
    #[ORM\OneToMany(mappedBy: 'refEmployed', targetEntity: Account::class)]
    private Collection $accounts;

    public function __construct()
    {
        $this->Customer = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->recos = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->addCollTransacs = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMaidenName(): ?string
    {
        return $this->maidenName;
    }

    public function setMaidenName(?string $maidenName): static
    {
        $this->maidenName = $maidenName;

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

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getReferent(): ?self
    {
        return $this->referent;
    }

    public function setReferent(?self $referent): self
    {
        $this->referent = $referent;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarName(?string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarSize(?int $avatarSize): void
    {
        $this->avatarSize = $avatarSize;
    }

    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
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

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

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
            $customer->setRefEmployed($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->Customer->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getRefEmployed() === $this) {
                $customer->setRefEmployed(null);
            }
        }

        return $this;
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
            $property->setRefEmployed($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->properties->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getRefEmployed() === $this) {
                $property->setRefEmployed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setRefEmployed($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getRefEmployed() === $this) {
                $project->setRefEmployed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setAuthor($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getAuthor() === $this) {
                $section->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setAuthor($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getAuthor() === $this) {
                $page->setAuthor(null);
            }
        }

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
            $contact->setForEmployed($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getForEmployed() === $this) {
                $contact->setForEmployed(null);
            }
        }

        return $this;
    }

    public function getEmployedPrez(): ?string
    {
        return $this->employedPrez;
    }

    public function setEmployedPrez(?string $employedPrez): self
    {
        $this->employedPrez = $employedPrez;

        return $this;
    }

    public function isIsWebpublish(): ?bool
    {
        return $this->isWebpublish;
    }

    public function setIsWebpublish(bool $isWebpublish): self
    {
        $this->isWebpublish = $isWebpublish;

        return $this;
    }

    public function getNumCollaborator(): ?string
    {
        return $this->numCollaborator;
    }

    public function setNumCollaborator(string $numCollaborator): static
    {
        $this->numCollaborator = $numCollaborator;

        return $this;
    }

    public function getUrlWeb(): ?string
    {
        return $this->urlWeb;
    }

    public function setUrlWeb(?string $urlWeb): static
    {
        $this->urlWeb = $urlWeb;

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
            $transaction->setRefEmployed($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getRefEmployed() === $this) {
                $transaction->setRefEmployed(null);
            }
        }

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
            $reco->setRefEmployed($this);
        }

        return $this;
    }

    public function removeReco(Reco $reco): static
    {
        if ($this->recos->removeElement($reco)) {
            // set the owning side to null (unless already changed)
            if ($reco->getRefEmployed() === $this) {
                $reco->setRefEmployed(null);
            }
        }

        return $this;
    }

    public function getDateEmployed(): ?\DateTimeInterface
    {
        return $this->dateEmployed;
    }

    public function setDateEmployed(?\DateTimeInterface $dateEmployed): static
    {
        $this->dateEmployed = $dateEmployed;

        return $this;
    }

    public function isIsSupprAvatar(): ?bool
    {
        return $this->isSupprAvatar;
    }

    public function setIsSupprAvatar(bool $isSupprAvatar): static
    {
        $this->isSupprAvatar = $isSupprAvatar;

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

    public function isIsGdpr(): ?bool
    {
        return $this->isGdpr;
    }

    public function setIsGdpr(bool $isGdpr): static
    {
        $this->isGdpr = $isGdpr;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setRefEmployed($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getRefEmployed() === $this) {
                $purchase->setRefEmployed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setRefEmployed($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getRefEmployed() === $this) {
                $cart->setRefEmployed(null);
            }
        }

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getCiFileName(): ?string
    {
        return $this->ciFileName;
    }

    public function setCiFileName(?string $ciFileName): static
    {
        $this->ciFileName = $ciFileName;

        return $this;
    }

    public function getCiFileext(): ?string
    {
        return $this->ciFileext;
    }

    public function setCiFileext(?string $ciFileext): static
    {
        $this->ciFileext = $ciFileext;

        return $this;
    }

    public function getCiFilesize(): ?int
    {
        return $this->ciFilesize;
    }

    public function setCiFilesize(?int $ciFilesize): static
    {
        $this->ciFilesize = $ciFilesize;

        return $this;
    }

    public function isIsSupprCi(): ?bool
    {
        return $this->isSupprCi;
    }

    public function setIsSupprCi(?bool $isSupprCi): static
    {
        $this->isSupprCi = $isSupprCi;

        return $this;
    }

    /**
     * @return Collection<int, AddCollTransac>
     */
    public function getAddCollTransacs(): Collection
    {
        return $this->addCollTransacs;
    }

    public function addAddCollTransac(AddCollTransac $addCollTransac): static
    {
        if (!$this->addCollTransacs->contains($addCollTransac)) {
            $this->addCollTransacs->add($addCollTransac);
            $addCollTransac->setRefemployed($this);
        }

        return $this;
    }

    public function removeAddCollTransac(AddCollTransac $addCollTransac): static
    {
        if ($this->addCollTransacs->removeElement($addCollTransac)) {
            // set the owning side to null (unless already changed)
            if ($addCollTransac->getRefemployed() === $this) {
                $addCollTransac->setRefemployed(null);
            }
        }

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): static
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setRefEmployed($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getRefEmployed() === $this) {
                $account->setRefEmployed(null);
            }
        }

        return $this;
    }
}
