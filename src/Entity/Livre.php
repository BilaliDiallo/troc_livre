<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $dateDePublication;

    /**
     * @ORM\Column(type="string", length=90)
     */
    private $langue;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombrePages;

    

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombrePoints;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponible;

   

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="livres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="LivresEcrit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="avoir")
     */
    private $proprietaire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $saisie;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="livre")
     */
    private $commandes;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $isbn;

   


    public function __construct()
    {
        $this->proprietaire = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateDePublication(): ?string
    {
        return $this->dateDePublication;
    }

    public function setDateDePublication(string $dateEdition): self
    {
        $this->dateDePublication = $dateEdition;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(int $nombrePages): self
    {
        $this->nombrePages = $nombrePages;

        return $this;
    }

    
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getNombrePoints(): ?int
    {
        return $this->nombrePoints;
    }

    public function setNombrePoints(int $nombrePoints): self
    {
        $this->nombrePoints = $nombrePoints;

        return $this;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getProprietaire(): Collection
    {
        return $this->proprietaire;
    }

    public function addProprietaire(User $proprietaire): self
    {   
        if (!$this->proprietaire->contains($proprietaire)) {
            $this->proprietaire[] = $proprietaire;
            $proprietaire->addAvoir($this);
        }

        return $this;
   
    }

    public function removeProprietaire(User $proprietaire): self
    {
        if ($this->proprietaire->removeElement($proprietaire)) {
            $proprietaire->removeAvoir($this);
        }

        return $this;
    }

    public function getSaisie(): ?bool
    {
        return $this->saisie;
    }

    public function setSaisie(bool $saisie): self
    {
        $this->saisie = $saisie;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setLivre($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getLivre() === $this) {
                $commande->setLivre(null);
            }
        }

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

   
}
