<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte qui utilise cette adresse email.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $Pseudo;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $civilite;

    
    /**
     * @ORM\Column(type="string", length=60)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vacance;

    /**
     * @ORM\ManyToMany(targetEntity=Livre::class, inversedBy="proprietaire")
     */
    private $avoir;

   

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commune;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $departement;

    
    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="commandeur")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="envoyeur")
     */
    private $envoies;

    /**
     * @ORM\Column(type="integer")
     */
    private $newDemande;

    /**
     * @ORM\Column(type="integer")
     */
    private $newEnvoie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pointsEmprunter;

    public function __construct()
    {
        $this->avoir = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->envoies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    
    public function RevoqueRoles(string $roles): self
    {
        while(in_array($roles, $this->roles)) {
          
            unset($this->roles[array_search($roles, $this->roles)]);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): self
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }


    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function addPoints(int $points): self
    {
        $this->points += $points;

        return $this;
    }

    public function getVacance(): ?bool
    {
        return $this->vacance;
    }

    public function setVacance(bool $vacance): self
    {
        $this->vacance = $vacance;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getAvoir(): Collection
    {
        return $this->avoir;
    }

    public function addAvoir(Livre $avoir): self
    {
        if (!$this->avoir->contains($avoir)) {
            $this->avoir[] = $avoir;
        }

        return $this;
    }

    public function removeAvoir(Livre $avoir): self
    {
        $this->avoir->removeElement($avoir);

        return $this;
    }


    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }
        public function getPays(): ?Pays
        {
            return $this->pays;
        }
    
        public function setPays(?Pays $pays): self
        {
            $this->pays = $pays;
    
            return $this;
        }

        public function getTelephone(): ?string
        {
            return $this->telephone;
        }

        public function setTelephone(string $telephone): self
        {
            $this->telephone = $telephone;

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
                $commande->setCommandeur($this);
            }

            return $this;
        }

        public function removeCommande(Commande $commande): self
        {
            if ($this->commandes->removeElement($commande)) {
                // set the owning side to null (unless already changed)
                if ($commande->getCommandeur() === $this) {
                    $commande->setCommandeur(null);
                }
            }

            return $this;
        }

        /**
         * @return Collection|Commande[]
         */
        public function getEnvoies(): Collection
        {
            return $this->envoies;
        }

        public function addEnvoie(Commande $envoie): self
        {
            if (!$this->envoies->contains($envoie)) {
                $this->envoies[] = $envoie;
                $envoie->setEnvoyeur($this);
            }

            return $this;
        }

        public function removeEnvoie(Commande $envoie): self
        {
            if ($this->envoies->removeElement($envoie)) {
                // set the owning side to null (unless already changed)
                if ($envoie->getEnvoyeur() === $this) {
                    $envoie->setEnvoyeur(null);
                }
            }

            return $this;
        }

        public function getNewDemande(): ?int
        {
            return $this->newDemande;
        }

        public function setNewDemande(?int $newDemande): self
        {
            $this->newDemande = $newDemande;

            return $this;
        }
        public function addNewDemande(?int $newDemande): self
        {
            $this->newDemande += $newDemande;

            return $this;
        }

        public function getNewEnvoie(): ?int
        {
            return $this->newEnvoie;
        }

        public function setNewEnvoie(?int $newEnvoie): self
        {
            $this->newEnvoie = $newEnvoie;

            return $this;
        }

        public function addNewEnvoie(?int $newEnvoie): self
        {
            $this->newEnvoie += $newEnvoie;

            return $this;
        }

        public function getPointsEmprunter(): ?int
        {
            return $this->pointsEmprunter;
        }

        public function setPointsEmprunter(?int $pointsEmprunter): self
        {
            $this->pointsEmprunter = $pointsEmprunter;

            return $this;
        }
   
}
