<?php

namespace App\Entity\Webapp;

use App\Entity\Admin\Employed;
use App\Entity\Webapp\choice\Category;
use App\Repository\Webapp\ArticlesRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
#[Vich\Uploadable]
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

    /**
     * Insertion de l'image mise en avant liée à un article
     * NOTE : Il ne s'agit pas d'un champ mappé des métadonnées de l'entité, mais d'une simple propriété.
     **/
    #[UploadableField(mapping: "article_front", fileNameProperty: 'articleFrontName', size: 'articleFrontSize')]
    private $articleFrontFile;

    /**
     * Nom du fichier
     */
    #[ORM\Column(type:'string', nullable: true)]
    private $articleFrontName;

    /**
     * Taille du fichier
     */
    #[ORM\Column(type:'integer', nullable: true)]
    private $articleFrontSize;

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

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $articleFrontFile
     */
    public function setArticleFrontFile(?File $articleFrontFile = null): void
    {
        $this->articleFrontFile = $articleFrontFile;

        if (null !== $articleFrontFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getArticleFrontFile(): ?File
    {
        return $this->articleFrontFile;
    }

    public function setArticleFrontName(?string $articleFrontName): void
    {
        $this->articleFrontName = $articleFrontName;
    }

    public function getArticleFrontName(): ?string
    {
        return $this->articleFrontName;
    }

    public function setArticleFrontSize(?int $articleFrontSize): void
    {
        $this->articleFrontSize = $articleFrontSize;
    }

    public function getArticleFrontSize(): ?int
    {
        return $this->articleFrontSize;
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
