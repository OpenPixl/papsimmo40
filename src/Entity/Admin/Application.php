<?php

namespace App\Entity\Admin;

use App\Repository\Admin\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
