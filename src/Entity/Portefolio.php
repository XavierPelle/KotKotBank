<?php

namespace App\Entity;

use App\Repository\PortefolioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortefolioRepository::class)]
class Portefolio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'portefolio', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $shareName = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getShareName(): ?string
    {
        return $this->shareName;
    }

    public function setShareName(?string $shareName): static
    {
        $this->shareName = $shareName;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }
}