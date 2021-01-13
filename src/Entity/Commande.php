<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Livre::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livre;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commandeur;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="envoies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $envoyeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $envoyer;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valider;

    /**
     * @ORM\Column(type="boolean")
     */
    private $refuser;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $livraison;

    /**
     * @ORM\Column(type="boolean")
     */
    private $supprimee;

     /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $dateValidation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;

        return $this;
    }

    public function getCommandeur(): ?User
    {
        return $this->commandeur;
    }

    public function setCommandeur(?User $commandeur): self
    {
        $this->commandeur = $commandeur;

        return $this;
    }

    public function getEnvoyeur(): ?User
    {
        return $this->envoyeur;
    }

    public function setEnvoyeur(?User $envoyeur): self
    {
        $this->envoyeur = $envoyeur;

        return $this;
    }

    public function getEnvoyer(): ?bool
    {
        return $this->envoyer;
    }

    public function setEnvoyer(bool $envoyer): self
    {
        $this->envoyer = $envoyer;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function getRefuser(): ?bool
    {
        return $this->refuser;
    }

    public function setRefuser(bool $refuser): self
    {
        $this->refuser = $refuser;

        return $this;
    }

    public function getLivraison(): ?string
    {
        return $this->livraison;
    }

    public function setLivraison(?string $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    public function getSupprimee(): ?bool
    {
        return $this->supprimee;
    }

    public function setSupprimee(bool $supprimee): self
    {
        $this->supprimee = $supprimee;

        return $this;
    }

    public function getDateValidation(): ?string
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?string $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }
}
