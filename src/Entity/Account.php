<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    private ?Client $client = null;

    #[ORM\Column(nullable: true)]
    private ?float $balance = null;

    #[ORM\Column(nullable: true)]
    private ?int $overdraft = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToOne(mappedBy: 'emeteurAccount', cascade: ['persist', 'remove'])]
    private ?Transaction $transaction = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->overdraft = 0;
        $this->date = new \dateTime();;
    }

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

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getOverdraft(): ?int
    {
        return $this->overdraft;
    }

    public function setOverdraft(?int $overdraft): static
    {
        $this->overdraft = $overdraft;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
    
    public function debit(float $amount): void
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
        } else {
            throw new \Exception("Solde insuffisant");
        }
    }

    public function credit(float $amount): void
    {
        $this->balance += $amount;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        // unset the owning side of the relation if necessary
        if ($transaction === null && $this->transaction !== null) {
            $this->transaction->setEmeteurAccount(null);
        }

        // set the owning side of the relation if necessary
        if ($transaction !== null && $transaction->getEmeteurAccount() !== $this) {
            $transaction->setEmeteurAccount($this);
        }

        $this->transaction = $transaction;

        return $this;
    }


}
