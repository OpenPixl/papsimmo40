<?php

namespace App\Entity\Admin;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Admin\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource()]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nameSite;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $sloganSite;

    #[ORM\Column(type: 'text', nullable: true)]
    private $descrSite;

    #[ORM\Column(type: 'boolean')]
    private $isOnline = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $adminEmail;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $adminWebmaster;

    #[ORM\Column(type: 'boolean')]
    private $isBlockmenufluid = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private $offlineMessage;

    #[ORM\Column(type: 'boolean')]
    private $isShowofflinemessage = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowofflinelogo = false;

    #[ORM\Column(type: 'boolean')]
    private $isShowtitlesitehome = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $urlFacebook;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $urlInstagram;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $urlLinkedin;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $urlGooglebusiness;


    #[ORM\Column(type: 'string', nullable: true)]
    #[Ignore]
    private $logoFile;

    #[ORM\Column(type: 'string', nullable: true)]
    private $logoName;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $logoSize;


    #[ORM\Column(type: 'string', nullable: true)]
    #[Ignore]
    private $faviconFile;

    #[ORM\Column(type: 'string', nullable: true)]
    private $faviconName;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $faviconSize;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_appli = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $host_appli = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_pwa = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $host_pwa = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameSite(): ?string
    {
        return $this->nameSite;
    }

    public function setNameSite(string $nameSite): self
    {
        $this->nameSite = $nameSite;

        return $this;
    }

    public function getSloganSite(): ?string
    {
        return $this->sloganSite;
    }

    public function setSloganSite(?string $sloganSite): self
    {
        $this->sloganSite = $sloganSite;

        return $this;
    }

    public function getDescrSite(): ?string
    {
        return $this->descrSite;
    }

    public function setDescrSite(?string $descrSite): self
    {
        $this->descrSite = $descrSite;

        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): self
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->adminEmail;
    }

    public function setAdminEmail(?string $adminEmail): self
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    public function getAdminWebmaster(): ?string
    {
        return $this->adminWebmaster;
    }

    public function setAdminWebmaster(?string $adminWebmaster): self
    {
        $this->adminWebmaster = $adminWebmaster;

        return $this;
    }

    public function getIsBlockmenufluid(): ?bool
    {
        return $this->isBlockmenufluid;
    }

    public function setIsBlockmenufluid(bool $isBlockmenufluid): self
    {
        $this->isBlockmenufluid = $isBlockmenufluid;

        return $this;
    }

    public function getOfflineMessage(): ?string
    {
        return $this->offlineMessage;
    }

    public function setOfflineMessage(?string $offlineMessage): self
    {
        $this->offlineMessage = $offlineMessage;

        return $this;
    }

    public function getIsShowofflinemessage(): ?bool
    {
        return $this->isShowofflinemessage;
    }

    public function setIsShowofflinemessage(bool $isShowofflinemessage): self
    {
        $this->isShowofflinemessage = $isShowofflinemessage;

        return $this;
    }

    public function getIsShowofflinelogo(): ?bool
    {
        return $this->isShowofflinelogo;
    }

    public function setIsShowofflinelogo(bool $isShowofflinelogo): self
    {
        $this->isShowofflinelogo = $isShowofflinelogo;

        return $this;
    }

    public function getIsShowtitlesitehome(): ?bool
    {
        return $this->isShowtitlesitehome;
    }

    public function setIsShowtitlesitehome(bool $isShowtitlesitehome): self
    {
        $this->isShowtitlesitehome = $isShowtitlesitehome;

        return $this;
    }

    public function getUrlFacebook(): ?string
    {
        return $this->urlFacebook;
    }

    public function setUrlFacebook(?string $urlFacebook): self
    {
        $this->urlFacebook = $urlFacebook;

        return $this;
    }

    public function getUrlInstagram(): ?string
    {
        return $this->urlInstagram;
    }

    public function setUrlInstagram(string $urlInstagram): self
    {
        $this->urlInstagram = $urlInstagram;

        return $this;
    }

    public function getUrlLinkedin(): ?string
    {
        return $this->urlLinkedin;
    }

    public function setUrlLinkedin(?string $urlLinkedin): self
    {
        $this->urlLinkedin = $urlLinkedin;

        return $this;
    }

    public function getUrlGooglebusiness(): ?string
    {
        return $this->urlGooglebusiness;
    }

    public function setUrlGooglebusiness(?string $urlGooglebusiness): self
    {
        $this->urlGooglebusiness = $urlGooglebusiness;

        return $this;
    }

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $logoFile
     */
    public function setLogoFile(?File $logoFile = null): void
    {
        $this->logoFile = $logoFile;

        if (null !== $logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoName(?string $logoName): void
    {
        $this->logoName = $logoName;
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function setLogoSize(?int $logoSize): void
    {
        $this->logoSize = $logoSize;
    }

    public function getLogoSize(): ?int
    {
        return $this->logoSize;
    }

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form),
     * assurez-vous qu'une instance de "UploadedFile" est injectée dans ce paramètre pour déclencher la mise à jour.
     * Si le paramètre de configuration 'inject_on_load' de ce bundle est défini sur 'true', ce setter doit être
     * capable d'accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $faviconFile
     */
    public function setfaviconFile(?File $faviconFile = null): void
    {
        $this->faviconFile = $faviconFile;

        if (null !== $faviconFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getfaviconFile(): ?File
    {
        return $this->faviconFile;
    }

    public function setfaviconName(?string $faviconName): void
    {
        $this->faviconName = $faviconName;
    }

    public function getfaviconName(): ?string
    {
        return $this->faviconName;
    }

    public function setfaviconSize(?int $faviconSize): void
    {
        $this->faviconSize = $faviconSize;
    }

    public function getfaviconSize(): ?int
    {
        return $this->faviconSize;
    }


    public function getUrlAppli(): ?string
    {
        return $this->url_appli;
    }

    public function setUrlAppli(string $url_appli): static
    {
        $this->url_appli = $url_appli;

        return $this;
    }

    public function getHostAppli(): ?string
    {
        return $this->host_appli;
    }

    public function setHostAppli(?string $host_appli): static
    {
        $this->host_appli = $host_appli;

        return $this;
    }

    public function getUrlPwa(): ?string
    {
        return $this->url_pwa;
    }

    public function setUrlPwa(string $url_pwa): static
    {
        $this->url_pwa = $url_pwa;

        return $this;
    }

    public function getHostPwa(): ?string
    {
        return $this->host_pwa;
    }

    public function setHostPwa(?string $host_pwa): static
    {
        $this->host_pwa = $host_pwa;

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
}
