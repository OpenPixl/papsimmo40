<?php

namespace App\Entity\Gestapp;

use App\Repository\Gestapp\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private $property;

    /**
     * Insertion de l'image mise en avant liée à un article
     * NOTE : Il ne s'agit pas d'un champ mappé des métadonnées de l'entité, mais d'une simple propriété.
     **/
    #[UploadableField(mapping: "article_front", fileNameProperty: 'galeryFrontName', size: 'galeryFrontSize')]
    private $galeryFrontFile;

    /**
     * Nom du fichier
     */
    #[ORM\Column(type:'string', nullable: true)]
    private $galeryFrontName;

    /**
     * Taille du fichier
     */
    #[ORM\Column(type:'integer', nullable: true)]
    private $galeryFrontSize;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $galeryFrontFile
     */
    public function setGaleryFrontFile(?File $galeryFrontFile = null): void
    {
        $this->galeryFrontFile = $galeryFrontFile;

        if (null !== $galeryFrontFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getGaleryFrontFile(): ?File
    {
        return $this->galeryFrontFile;
    }

    public function setGaleryFrontName(?string $galeryFrontName): void
    {
        $this->galeryFrontName = $galeryFrontName;
    }

    public function getGaleryFrontName(): ?string
    {
        return $this->galeryFrontName;
    }

    public function setGaleryFrontSize(?int $galeryFrontSize): void
    {
        $this->galeryFrontSize = $galeryFrontSize;
    }

    public function getGaleryFrontSize(): ?int
    {
        return $this->galeryFrontSize;
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
}
