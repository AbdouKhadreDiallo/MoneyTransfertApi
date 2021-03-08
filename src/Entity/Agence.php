<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      denormalizationContext={"groups"={"usersAgence:read"}},
 *      collectionOperations= {
 *          "post",
 *          "get",
 *          "users" = {
 *              "method" = "get",
 *              "route_name" = "usersAgence",
 *          },
 *      },
 *      itemOperations = {
 *          "put",
 *          "delete",
 *          "get" = {
 *              "defaults"={"id"=null},
 *          },
 *          "usersAgence" = {
 *              "method" = "get",
 *              "route_name" = "usersAgence"
 *          },
 *          "blockUser" = {
 *              "method" = "delete",
 *              "route_name"="block"
 *          }
 *      }
 * 
 * 
 * )
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 */
class Agence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"usersAgence:read"})
     * @Groups({"getByCode:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"usersAgence:read"})
     * @Groups({"getByCode:read"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"usersAgence:read"})
     * @Groups({"getByCode:read"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"usersAgence:read"})
     * @Groups({"getByCode:read"})
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=AdminAgence::class, mappedBy="agence")
     * @Groups({"usersAgence:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"getByCode:read"})
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=UserAgence::class, mappedBy="agence")
     * @Groups({"usersAgence:read"})
     * @Groups({"usersAgence:read"})
     */
    private $usersAgence;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"getByCode:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive = false;

    public function __construct()
    {
        $this->admin = new ArrayCollection();
        $this->usersAgence = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

   

    /**
     * @return Collection|AdminAgence[]
     */
    public function getAdmin(): Collection
    {
        return $this->admin;
    }

    public function addAdmin(AdminAgence $admin): self
    {
        if (!$this->admin->contains($admin)) {
            $this->admin[] = $admin;
            $admin->setAgence($this);
        }

        return $this;
    }

    public function removeAdmin(AdminAgence $admin): self
    {
        if ($this->admin->removeElement($admin)) {
            // set the owning side to null (unless already changed)
            if ($admin->getAgence() === $this) {
                $admin->setAgence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAgence[]
     */
    public function getUsersAgence(): Collection
    {
        return $this->usersAgence;
    }

    public function addUsersAgence(UserAgence $usersAgence): self
    {
        if (!$this->usersAgence->contains($usersAgence)) {
            $this->usersAgence[] = $usersAgence;
            $usersAgence->setAgence($this);
        }

        return $this;
    }

    public function removeUsersAgence(UserAgence $usersAgence): self
    {
        if ($this->usersAgence->removeElement($usersAgence)) {
            // set the owning side to null (unless already changed)
            if ($usersAgence->getAgence() === $this) {
                $usersAgence->setAgence(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
