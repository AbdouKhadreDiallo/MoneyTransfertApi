<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminSystemRepository;

/**
 * @ORM\Entity(repositoryClass=AdminSystemRepository::class)
 */
class AdminSystem extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity=Comptes::class, mappedBy="creator")
     */
    private $comptesCreated;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="adminSystem")
     */
    private $depots;

    public function __construct()
    {
        $this->comptesCreated = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Comptes[]
     */
    public function getComptesCreated(): Collection
    {
        return $this->comptesCreated;
    }

    public function addComptesCreated(Comptes $comptesCreated): self
    {
        if (!$this->comptesCreated->contains($comptesCreated)) {
            $this->comptesCreated[] = $comptesCreated;
            $comptesCreated->setCreator($this);
        }

        return $this;
    }

    public function removeComptesCreated(Comptes $comptesCreated): self
    {
        if ($this->comptesCreated->removeElement($comptesCreated)) {
            // set the owning side to null (unless already changed)
            if ($comptesCreated->getCreator() === $this) {
                $comptesCreated->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setAdminSystem($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getAdminSystem() === $this) {
                $depot->setAdminSystem(null);
            }
        }

        return $this;
    }

    
}
