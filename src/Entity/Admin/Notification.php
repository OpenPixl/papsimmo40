<?php

namespace App\Entity\Admin;

use App\Repository\Admin\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Employed $refEmployed = null;

    #[ORM\Column]
    private ?bool $isConnectedAt = null;

    #[ORM\Column]
    private ?bool $isApi = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $log = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefEmployed(): ?Employed
    {
        return $this->refEmployed;
    }

    public function setRefEmployed(?Employed $refEmployed): static
    {
        $this->refEmployed = $refEmployed;

        return $this;
    }

    public function isIsConnectedAt(): ?bool
    {
        return $this->isConnectedAt;
    }

    public function setIsConnectedAt(bool $isConnectedAt): static
    {
        $this->isConnectedAt = $isConnectedAt;

        return $this;
    }

    public function isIsApi(): ?bool
    {
        return $this->isApi;
    }

    public function setIsApi(bool $isApi): static
    {
        $this->isApi = $isApi;

        return $this;
    }

    public function getLog(): ?array
    {
        return $this->log;
    }

    public function setLog(?array $log): static
    {
        $this->log = $log;

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
}
