<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
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
    private ?float $probability = null;

    #[ORM\Column(nullable: true)]
    private ?float $impact = null;

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

    public function getProbability(): ?float
    {
        return $this->probability;
    }

    public function setProbability(?float $probability): static
    {
        $this->probability = $probability;

        return $this;
    }

    public function getImpact(): ?float
    {
        return $this->impact;
    }

    public function setImpact(?float $impact): static
    {
        $this->impact = $impact;

        return $this;
    }
}
