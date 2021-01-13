<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */
class Auteur
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
    private $nomComplet;

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true)
     */
    private $LivresEcrit;

    public function __construct()
    {
        $this->LivresEcrit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getLivresEcrit(): Collection
    {
        return $this->LivresEcrit;
    }

    public function addLivresEcrit(Livre $livresEcrit): self
    {
        if (!$this->LivresEcrit->contains($livresEcrit)) {
            $this->LivresEcrit[] = $livresEcrit;
            $livresEcrit->setAuteur($this);
        }

        return $this;
    }

    public function removeLivresEcrit(Livre $livresEcrit): self
    {
        if ($this->LivresEcrit->removeElement($livresEcrit)) {
            // set the owning side to null (unless already changed)
            if ($livresEcrit->getAuteur() === $this) {
                $livresEcrit->setAuteur(null);
            }
        }

        return $this;
    }
}
