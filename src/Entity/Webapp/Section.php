<?php

namespace App\Entity\Webapp;

use App\Entity\Admin\Employed;
use App\Entity\Webapp\choice\Category;
use App\Repository\Webapp\SectionRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToOne(targetEntity: Articles::class, cascade: ['persist', 'remove'])]
    private $oneArticle;

    #[ORM\OneToOne(targetEntity: Category::class, cascade: ['persist', 'remove'])]
    private $OneCategory;

    #[ORM\OneToOne(targetEntity: Employed::class, cascade: ['persist', 'remove'])]
    private $oneEmployed;

    #[ORM\Column(type: 'boolean')]
    private $isShowtitle = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowdescription = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowdate = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $positionFavorite;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isSectionfluid = false;

    #[ORM\Column(type: 'string', length: 100)]
    private $baliseClass;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $baliseId;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $baliseName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $baliseStyle;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'sections')]
    private $author;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'sections')]
    private $page;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOneArticle(): ?Articles
    {
        return $this->oneArticle;
    }

    public function setOneArticle(?Articles $oneArticle): self
    {
        $this->oneArticle = $oneArticle;

        return $this;
    }

    public function getOneCategory(): ?Category
    {
        return $this->OneCategory;
    }

    public function setOneCategory(?Category $OneCategory): self
    {
        $this->OneCategory = $OneCategory;

        return $this;
    }

    public function getOneEmployed(): ?Employed
    {
        return $this->oneEmployed;
    }

    public function setOneEmployed(?Employed $oneEmployed): self
    {
        $this->oneEmployed = $oneEmployed;

        return $this;
    }

    public function getIsShowtitle(): ?bool
    {
        return $this->isShowtitle;
    }

    public function setIsShowtitle(bool $isShowtitle): self
    {
        $this->isShowtitle = $isShowtitle;

        return $this;
    }

    public function getIsShowdescription(): ?bool
    {
        return $this->isShowdescription;
    }

    public function setIsShowdescription(bool $isShowdescription): self
    {
        $this->isShowdescription = $isShowdescription;

        return $this;
    }

    public function getIsShowdate(): ?bool
    {
        return $this->isShowdate;
    }

    public function setIsShowdate(bool $isShowdate): self
    {
        $this->isShowdate = $isShowdate;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPositionFavorite(): ?int
    {
        return $this->positionFavorite;
    }

    public function setPositionFavorite(int $positionFavorite): self
    {
        $this->positionFavorite = $positionFavorite;

        return $this;
    }

    public function getIsSectionfluid(): ?bool
    {
        return $this->isSectionfluid;
    }

    public function setIsSectionfluid(bool $isSectionfluid): self
    {
        $this->isSectionfluid = $isSectionfluid;

        return $this;
    }

    public function getBaliseClass(): ?string
    {
        return $this->baliseClass;
    }

    public function setBaliseClass(string $baliseClass): self
    {
        $this->baliseClass = $baliseClass;

        return $this;
    }

    public function getBaliseId(): ?string
    {
        return $this->baliseId;
    }

    public function setBaliseId(?string $baliseId): self
    {
        $this->baliseId = $baliseId;

        return $this;
    }

    public function getBaliseName(): ?string
    {
        return $this->baliseName;
    }

    public function setBaliseName(?string $baliseName): self
    {
        $this->baliseName = $baliseName;

        return $this;
    }

    public function getBaliseStyle(): ?string
    {
        return $this->baliseStyle;
    }

    public function setBaliseStyle(?string $baliseStyle): self
    {
        $this->baliseStyle = $baliseStyle;

        return $this;
    }

    public function getAuthor(): ?Employed
    {
        return $this->author;
    }

    public function setAuthor(?Employed $author): self
    {
        $this->author = $author;

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

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }
}
