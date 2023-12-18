<?php

namespace App\Entity;


use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity('email')]
#[ORM\EntityListeners(['App\EntityListener\ClientListener'])]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $investorProfil = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastConnexion = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Portefolio $portefolio = null;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?ShareTransaction $shareTransaction = null;

    public function __construct()
    {
        $this->actionSavingsAccounts = new ArrayCollection();
        $this->registrationDate = new \dateTime();
        $this->roles = ["ROLE_USER"];
        $this->accounts = new ArrayCollection();
        $this->investorProfil = "CLASSIC_PROFIL";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return array('ROLE_USER');
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getInvestorProfil(): ?string
    {
        return $this->investorProfil;
    }

    public function setInvestorProfil(string $investorProfil): static
    {
        $this->investorProfil = $investorProfil;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTimeInterface $lastConnexion): static
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setClient($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getClient() === $this) {
                $account->setClient(null);
            }
        }

        return $this;
    }

    public function getPortefolio(): ?Portefolio
    {
        return $this->portefolio;
    }

    public function setPortefolio(?Portefolio $portefolio): static
    {
        // unset the owning side of the relation if necessary
        if ($portefolio === null && $this->portefolio !== null) {
            $this->portefolio->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($portefolio !== null && $portefolio->getClient() !== $this) {
            $portefolio->setClient($this);
        }

        $this->portefolio = $portefolio;

        return $this;
    }

    public function getShareTransaction(): ?ShareTransaction
    {
        return $this->shareTransaction;
    }

    public function setShareTransaction(?ShareTransaction $shareTransaction): static
    {
        // unset the owning side of the relation if necessary
        if ($shareTransaction === null && $this->shareTransaction !== null) {
            $this->shareTransaction->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($shareTransaction !== null && $shareTransaction->getClient() !== $this) {
            $shareTransaction->setClient($this);
        }

        $this->shareTransaction = $shareTransaction;

        return $this;
    }
}
