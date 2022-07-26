<?php

namespace App\Entity\Admin;

use App\Entity\Gestapp\Customer;
use App\Repository\Admin\ContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    private $home;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    private $desk;

    #[ORM\Column(type: 'string', length: 14)]
    private $gsm;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    private $fax;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $otherEmail;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $facebook;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $instagram;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $linkedin;

    #[ORM\ManyToOne(targetEntity: Employed::class)]
    private $employed;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: true)]
    private $Customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHome(): ?string
    {
        return $this->home;
    }

    public function setHome(?string $home): self
    {
        $this->home = $home;

        return $this;
    }

    public function getDesk(): ?string
    {
        return $this->desk;
    }

    public function setDesk(?string $desk): self
    {
        $this->desk = $desk;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(string $gsm): self
    {
        $this->gsm = $gsm;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getOtherEmail(): ?string
    {
        return $this->otherEmail;
    }

    public function setOtherEmail(?string $otherEmail): self
    {
        $this->otherEmail = $otherEmail;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getEmployed(): ?Employed
    {
        return $this->employed;
    }

    public function setEmployed(?Employed $employed): self
    {
        $this->employed = $employed;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->Customer;
    }

    public function setCustomer(?Customer $Customer): self
    {
        $this->Customer = $Customer;

        return $this;
    }
}
