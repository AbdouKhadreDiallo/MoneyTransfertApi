<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepotRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource(
 *  collectionOperations= {
 *          "get",
 *          "depot" = {
 *              "method" = "post",
 *              "route_name" = "depotzer",
 *          },
 *          "depotUser" = {
 *              "method" = "get",
 *              "route_name" = "userDepots",
 *          },
 *      },
 *      itemOperations = {
 *          "put",
 *          "delete",
 *          "get"
 *      }
 * )
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="depots")
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=Caissier::class, inversedBy="depots")
     */
    private $caissier;

    /**
     * @ORM\ManyToOne(targetEntity=AdminSystem::class, inversedBy="depots")
     */
    private $adminSystem;

    public function __construct()
    {
        $this->dateDepot = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCompte(): ?Comptes
    {
        return $this->compte;
    }

    public function setCompte(?Comptes $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getCaissier(): ?Caissier
    {
        return $this->caissier;
    }

    public function setCaissier(?Caissier $caissier): self
    {
        $this->caissier = $caissier;

        return $this;
    }

    public function getAdminSystem(): ?AdminSystem
    {
        return $this->adminSystem;
    }

    public function setAdminSystem(?AdminSystem $adminSystem): self
    {
        $this->adminSystem = $adminSystem;

        return $this;
    }
}
