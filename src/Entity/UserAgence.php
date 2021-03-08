<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserAgenceRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      normalizationContext = {"groups" = {"userrAgence:read"}},
 * )
 * @ORM\Entity(repositoryClass=UserAgenceRepository::class)
 */
class UserAgence extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"userrAgence:read"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="usersAgence")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userAuthorDepot")
     */
    private $transactionDepot;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userAuthorRetrait")
     */
    private $transactionRetrait;

    public function __construct()
    {
        $this->transactionDepot = new ArrayCollection();
        $this->transactionRetrait = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionDepot(): Collection
    {
        return $this->transactionDepot;
    }

    public function addTransactionDepot(Transaction $transactionDepot): self
    {
        if (!$this->transactionDepot->contains($transactionDepot)) {
            $this->transactionDepot[] = $transactionDepot;
            $transactionDepot->setUserAuthorDepot($this);
        }

        return $this;
    }

    public function removeTransactionDepot(Transaction $transactionDepot): self
    {
        if ($this->transactionDepot->removeElement($transactionDepot)) {
            // set the owning side to null (unless already changed)
            if ($transactionDepot->getUserAuthorDepot() === $this) {
                $transactionDepot->setUserAuthorDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionRetrait(): Collection
    {
        return $this->transactionRetrait;
    }

    public function addTransactionRetrait(Transaction $transactionRetrait): self
    {
        if (!$this->transactionRetrait->contains($transactionRetrait)) {
            $this->transactionRetrait[] = $transactionRetrait;
            $transactionRetrait->setUserAuthorRetrait($this);
        }

        return $this;
    }

    public function removeTransactionRetrait(Transaction $transactionRetrait): self
    {
        if ($this->transactionRetrait->removeElement($transactionRetrait)) {
            // set the owning side to null (unless already changed)
            if ($transactionRetrait->getUserAuthorRetrait() === $this) {
                $transactionRetrait->setUserAuthorRetrait(null);
            }
        }

        return $this;
    }

    
}
