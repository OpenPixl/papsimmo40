<?php

namespace App\Entity\Webapp;

use App\Entity\Admin\Employed;
use App\Repository\Webapp\PageRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'pages')]
    private $author;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private $isShowtitle = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowdate = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowdescription = false;

    #[ORM\Column(type: 'boolean')]
    private $isMenu = false;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $state;

    #[ORM\Column(type: 'array', nullable: true)]
    private $metaKeywords = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private $MetaDescrition;

    #[ORM\Column(type: 'array', nullable: true)]
    private $tag = [];

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $parent;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $seoTitle;

    #[ORM\OneToMany(mappedBy: 'page', targetEntity: Section::class)]
    private $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
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

    public function getAuthor(): ?Employed
    {
        return $this->author;
    }

    public function setAuthor(?Employed $author): self
    {
        $this->author = $author;

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

    public function getIsShowtitle(): ?bool
    {
        return $this->isShowtitle;
    }

    public function setIsShowtitle(bool $isShowtitle): self
    {
        $this->isShowtitle = $isShowtitle;

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

    public function getIsShowdescription(): ?bool
    {
        return $this->isShowdescription;
    }

    public function setIsShowdescription(bool $isShowdescription): self
    {
        $this->isShowdescription = $isShowdescription;

        return $this;
    }

    public function getIsMenu(): ?bool
    {
        return $this->isMenu;
    }

    public function setIsMenu(bool $isMenu): self
    {
        $this->isMenu = $isMenu;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMetaKeywords(): ?array
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?array $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function getMetaDescrition(): ?string
    {
        return $this->MetaDescrition;
    }

    public function setMetaDescrition(?string $MetaDescrition): self
    {
        $this->MetaDescrition = $MetaDescrition;

        return $this;
    }

    public function getTag(): ?array
    {
        return $this->tag;
    }

    public function setTag(?array $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

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

    public function __tostring()
    {
        return $this->name;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): self
    {
        $this->seoTitle = $seoTitle;

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
            $section->setPage($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getPage() === $this) {
                $section->setPage(null);
            }
        }

        return $this;
    }
}
