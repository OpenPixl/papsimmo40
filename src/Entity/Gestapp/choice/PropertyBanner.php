<?php

namespace App\Entity\Gestapp\choice;

use App\Repository\Gestapp\choice\PropertyBannerRepository;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyBannerRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PropertyBanner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $bannerFilename;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
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

    public function getBannerFilename()
    {
        return $this->bannerFilename;
    }

    public function setBannerFilename($bannerFilename)
    {
        $this->bannerFilename = $bannerFilename;

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
}
