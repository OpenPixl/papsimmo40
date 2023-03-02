<?php

namespace App\Entity\Gestapp;

use App\Entity\Gestapp\choice\CatDocument;
use App\Repository\Gestapp\DocumentRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(length: 25)]
    private ?string $typeDoc = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pdf = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $doc = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $sheet = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?CatDocument $category = null;

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

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getDoc(): ?string
    {
        return $this->doc;
    }

    public function setDoc(?string $doc): self
    {
        $this->doc = $doc;

        return $this;
    }

    public function getSheet(): ?string
    {
        return $this->sheet;
    }

    public function setSheet(?string $sheet): self
    {
        $this->sheet = $sheet;

        return $this;
    }

    public function getTypeDoc(): ?string
    {
        return $this->typeDoc;
    }

    public function setTypeDoc(string $typeDoc): self
    {
        $this->typeDoc = $typeDoc;

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

    public function getCategory(): ?CatDocument
    {
        return $this->category;
    }

    public function setCategory(?CatDocument $category): self
    {
        $this->category = $category;

        return $this;
    }
}
