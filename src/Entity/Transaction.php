<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      normalizationContext = {"groups"={"read"}},
 *      collectionOperations= {
 *          "post",
 *          "get",
 *          "depots" = {
 *              "method" = "POST",
 *              "route_name" = "depot",
 *          },
 *          "retraits" = {
 *              "method" = "POST",
 *              "route_name" = "retrait",
 *          },
 *          "usersTrans" = {
 *              "method" = "get",
 *              "route_name"="myTransactions"
 *          },
 *          "statePart" = {
 *              "method" = "get",
 *              "route_name" = "partEtat"
 *          },
 *          "Getcode" = {
 *              "method" = "get",
 *              "route_name" = "GetCode"
 *          },
 *          "trie" = {
 *              "method" = "get",
 *              "route_name" = "trier"
 *          },
 *          "commissions" = {
 *              "method" = "get",
 *              "route_name" = "commissions"
 *          },
 *          "all" = {
 *              "method" = "get",
 *              "route_name" = "all"
 *          },
 *           "alls" = {
 *              "method" = "get",
 *              "route_name" = "alls"
 *          }
 * 
 *      },
 *       itemOperations = {
 *          "put",
 *          "get" = {
 *              "defaults"={"id"=null},
 *          },
 *          
 *      }
 *      
 * )
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"bycode:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"usersDepot:read"})
     * @Groups({"partEtat:read"})
     * @Groups({"bycode:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date",nullable=true)
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     * 
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"usersDepot:read"})
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $codeTransmission;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"bycode:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $frais;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $fraisDepot;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"partEtat:read"})
     * @Groups({"read"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"usersDepot:read"})
     * @Groups({"read"})
     */
    private $fraisSystem;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="transactions")
     * @Groups({"read"})
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=UserAgence::class, inversedBy="transactionDepot")
     * @Groups({"allTransaction:read"})
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     * 
     */
    private $userAuthorDepot;

    /**
     * @ORM\ManyToOne(targetEntity=UserAgence::class, inversedBy="transactionRetrait")
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     */
    private $userAuthorRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=AdminAgence::class, inversedBy="transactionDepot")
     * @Groups({"allTransaction:read"})
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $adminSystemAuthorDepot;

    /**
     * @ORM\ManyToOne(targetEntity=AdminAgence::class, inversedBy="transactionRetrait")
     * @Groups({"allTransaction:read"})
     * @Groups({"bycode:read"})
     */
    private $adminSystemAuthorRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="depotTransaction")
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="retraitTransaction")
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $receiver;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read"})
     */
    private $isFinished = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="transactionRetrait")
     * @Groups({"read"})
     */
    private $compteRetrait;

    public function __construct()
    {
        $this->dateDepot = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getCodeTransmission(): ?string
    {
        return $this->codeTransmission;
    }

    public function setCodeTransmission(string $codeTransmission): self
    {
        $this->codeTransmission = $codeTransmission;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getFraisDepot(): ?int
    {
        return $this->fraisDepot;
    }

    public function setFraisDepot(int $fraisDepot): self
    {
        $this->fraisDepot = $fraisDepot;

        return $this;
    }

    public function getFraisRetrait(): ?int
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(int $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getFraisEtat(): ?int
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(int $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSystem(): ?int
    {
        return $this->fraisSystem;
    }

    public function setFraisSystem(int $fraisSystem): self
    {
        $this->fraisSystem = $fraisSystem;

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


    public function getUserAuthorDepot(): ?UserAgence
    {
        return $this->userAuthorDepot;
    }

    public function setUserAuthorDepot(?UserAgence $userAuthorDepot): self
    {
        $this->userAuthorDepot = $userAuthorDepot;

        return $this;
    }

    public function getUserAuthorRetrait(): ?UserAgence
    {
        return $this->userAuthorRetrait;
    }

    public function setUserAuthorRetrait(?UserAgence $userAuthorRetrait): self
    {
        $this->userAuthorRetrait = $userAuthorRetrait;

        return $this;
    }

    public function getAdminSystemAuthorDepot(): ?AdminAgence
    {
        return $this->adminSystemAuthorDepot;
    }

    public function setAdminSystemAuthorDepot(?AdminAgence $adminSystemAuthorDepot): self
    {
        $this->adminSystemAuthorDepot = $adminSystemAuthorDepot;

        return $this;
    }

    public function getAdminSystemAuthorRetrait(): ?AdminAgence
    {
        return $this->adminSystemAuthorRetrait;
    }

    public function setAdminSystemAuthorRetrait(?AdminAgence $adminSystemAuthorRetrait): self
    {
        $this->adminSystemAuthorRetrait = $adminSystemAuthorRetrait;

        return $this;
    }

    public function getSender(): ?Client
    {
        return $this->sender;
    }

    public function setSender(?Client $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?Client
    {
        return $this->receiver;
    }

    public function setReceiver(?Client $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getIsFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): self
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCompteRetrait(): ?Comptes
    {
        return $this->compteRetrait;
    }

    public function setCompteRetrait(?Comptes $compteRetrait): self
    {
        $this->compteRetrait = $compteRetrait;

        return $this;
    }

    
}
