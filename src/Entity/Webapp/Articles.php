<?php

namespace App\Entity\Webapp;

use App\Entity\Admin\Employed;
use App\Entity\Webapp\choice\Category;
use App\Repository\Webapp\ArticlesRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Articles
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
    private $content;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private $category;

    #[ORM\ManyToOne(targetEntity: Employed::class, inversedBy: 'articles')]
    private $author;

    #[ORM\Column(type: 'boolean')]
    private $isShowtitle = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowdate = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowreadmore = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isLink = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $linkText;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $state;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getIsShowreadmore(): ?bool
    {
        return $this->isShowreadmore;
    }

    public function setIsShowreadmore(bool $isShowreadmore): self
    {
        $this->isShowreadmore = $isShowreadmore;

        return $this;
    }

    public function getIsLink(): ?bool
    {
        return $this->isLink;
    }

    public function setIsLink(?bool $isLink): self
    {
        $this->isLink = $isLink;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkText(?string $linkText): self
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
