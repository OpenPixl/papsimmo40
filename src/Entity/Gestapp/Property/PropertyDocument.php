<?php

namespace App\Entity\Gestapp\Property;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\Property\PropertyDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyDocumentRepository::class)]
class PropertyDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $documentName = null;

    #[ORM\Column(nullable: true)]
    private ?int $documentSize = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $documentExt = null;

    #[ORM\ManyToOne(inversedBy: 'propertyDocuments')]
    private ?Property $property = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentName(): ?string
    {
        return $this->documentName;
    }

    public function setDocumentName(?string $documentName): static
    {
        $this->documentName = $documentName;

        return $this;
    }

    public function getDocumentSize(): ?int
    {
        return $this->documentSize;
    }

    public function setDocumentSize(?int $documentSize): static
    {
        $this->documentSize = $documentSize;

        return $this;
    }

    public function getDocumentExt(): ?string
    {
        return $this->documentExt;
    }

    public function setDocumentExt(?string $documentExt): static
    {
        $this->documentExt = $documentExt;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }
}
