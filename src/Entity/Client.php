<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $CNI;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="sender")
     */
    private $depotTransaction;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="receiver")
     */
    private $retraitTransaction;

    public function __construct()
    {
        $this->depotTransaction = new ArrayCollection();
        $this->retraitTransaction = new ArrayCollection();
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCNI(): ?string
    {
        return $this->CNI;
    }

    public function setCNI(string $CNI): self
    {
        $this->CNI = $CNI;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getDepotTransaction(): Collection
    {
        return $this->depotTransaction;
    }

    public function addDepotTransaction(Transaction $depotTransaction): self
    {
        if (!$this->depotTransaction->contains($depotTransaction)) {
            $this->depotTransaction[] = $depotTransaction;
            $depotTransaction->setSender($this);
        }

        return $this;
    }

    public function removeDepotTransaction(Transaction $depotTransaction): self
    {
        if ($this->depotTransaction->removeElement($depotTransaction)) {
            // set the owning side to null (unless already changed)
            if ($depotTransaction->getSender() === $this) {
                $depotTransaction->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraitTransaction(): Collection
    {
        return $this->retraitTransaction;
    }

    public function addRetraitTransaction(Transaction $retraitTransaction): self
    {
        if (!$this->retraitTransaction->contains($retraitTransaction)) {
            $this->retraitTransaction[] = $retraitTransaction;
            $retraitTransaction->setReceiver($this);
        }

        return $this;
    }

    public function removeRetraitTransaction(Transaction $retraitTransaction): self
    {
        if ($this->retraitTransaction->removeElement($retraitTransaction)) {
            // set the owning side to null (unless already changed)
            if ($retraitTransaction->getReceiver() === $this) {
                $retraitTransaction->setReceiver(null);
            }
        }

        return $this;
    }

    
}
