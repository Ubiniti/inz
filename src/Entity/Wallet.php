<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WalletRepository")
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="wallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $funds;

    /**
     * Wallet constructor.
     *
     * @param $funds
     */
    public function __construct() { $this->funds = 0; }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFunds(): ?int
    {
        return $this->funds;
    }

    public function getFundsAsCurrency(): float
    {
        return ((float)$this->funds)/100;
    }

    public function setFunds(?int $funds): self
    {
        $this->funds = $funds;

        return $this;
    }
}
