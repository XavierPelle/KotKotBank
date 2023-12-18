<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $domain = null;

    #[ORM\Column(nullable: true)]
    private ?float $sharePrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $shareQuantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getSharePrice(): ?float
    {
        return $this->sharePrice;
    }

    public function setSharePrice(?float $sharePrice): static
    {
        $this->sharePrice = $sharePrice;

        return $this;
    }

    public function getShareQuantity(): ?int
    {
        return $this->shareQuantity;
    }

    public function setShareQuantity(?int $shareQuantity): static
    {
        $this->shareQuantity = $shareQuantity;

        return $this;
    }

    public function jsonSerialize() {
        // Return an associative array representing the object
        return [
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain,
            'sharePrice' => $this->sharePrice,
            'shareQuantity' => $this->shareQuantity,
        ];
    }
}
