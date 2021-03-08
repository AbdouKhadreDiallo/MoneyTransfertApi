<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ApiResource(
 *  collectionOperations= {
 *          "post",
 *          "get",
 *      },
 *      itemOperations = {
 *          "put",
 *          "get" = {
 *              "defaults"={"id"=null},
 *          },
 *          
 *      }
 * 
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "admin" = "AdminSystem", "caissier" = "Caissier", "adminagence" = "AdminAgence" , "useragence" = "UserAgence"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"usersAgence:read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"caissier:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"adminAgence:read"})
     * @Groups({"userrAgence:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"bycode:read"})
     * @Groups({"read"})
     */
    private $email;

    
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"caissier:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"adminAgence:read"})
     * @Groups({"userrAgence:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"read"})
     * @Groups({"getByCode:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"caissier:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"adminAgence:read"})
     * @Groups({"userrAgence:read"})
     * @Groups({"allTransaction:read"})
     * @Groups({"getByCode:read"})
     * @Groups({"read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"caissier:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"adminAgence:read"})
     * @Groups({"userrAgence:read"})
     */
    private $CIN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"caissier:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"usersAgence:read"})
     * @Groups({"adminAgence:read"})
     * @Groups({"userrAgence:read"})
     * @Groups({"getByCode:read"})
     * @Groups({"read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"caissier:read"})
     */
    private $isActive = true;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @Groups({"caissier:read"})
     */
    private $profil;

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

    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = \str_replace(' ', '_', 'ROLE_'.$this->profil->getLibelle());

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(?string $CIN): self
    {
        $this->CIN = $CIN;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }
}
