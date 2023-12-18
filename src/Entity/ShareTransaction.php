<?php

namespace App\Entity;

use App\Repository\ShareTransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShareTransactionRepository::class)]
class ShareTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $shareName = null;

    #[ORM\Column(nullable: true)]
    private ?float $sharePrice = null;

    #[ORM\OneToOne(inversedBy: 'shareTransaction', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSharePrice(): ?float
    {
        return $this->sharePrice;
    }

    public function setSharePrice(?float $sharePrice): static
    {
        $this->sharePrice = $sharePrice;

        return $this;
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
}
