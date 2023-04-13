<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name:'clients')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idClient')]
    private ?int $idClient = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message:"you're email adress {{ value }} is invalid")]
    private ?string $email = null;

    #[ORM\Column(name:'firstName')]
    #[Assert\Length(min:2, minMessage:"You're name must have {{ limit }} character minimun")]
    #[Assert\Length(max:30, maxMessage:"You're name must have {{ limit }} character maximum")]
    private ?string $firstName = null;

    #[ORM\Column(name:'lastName')]
    #[Assert\Length(min:2, minMessage:"You're last name must have {{ limit }} character minimun")]
    #[Assert\Length(max:30, maxMessage:"You're last name must have {{ limit }} character maximum")]
    private ?string $lastName = null;

    #[ORM\Column(name:'adress')]
    #[Assert\Length(min:5, minMessage:"You're adress must have {{ limit }} character minimun")]
    #[Assert\Length(max:100, maxMessage:"You're adress must have {{ limit }} character maximum")]
    private ?string $adress = null;

    #[ORM\Column(name:'city')]
    #[Assert\Length(min:3, minMessage:"The city must have {{ limit }} character minimun")]
    #[Assert\Length(max:30, maxMessage:"The city name must have {{ limit }} character maximum")]
    private ?string $city = null;

    #[ORM\Column(name:'postalCode')]
    #[Assert\Regex(pattern:"/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ -]?\d[ABCEGHJ-NPRSTV-Z]\d$/i", message:"the postal code must respect the format")]
    private ?string $postalCode = null;

    #[ORM\Column(length:20, nullable:true)]
    #[Assert\Regex(pattern:"/^[0-9]{10}$/", message:"You're phone number must contained 10 number")]
    private ?string $phone = null;

    #[ORM\Column(name:'province',length:2)]
    private ?string $province   = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getIdClient(): ?int
    {
        return $this->idClient;
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
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
    public function getAdress(): ?string
    {
        return $this->adress;
    }
    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }
    public function getCity(): ?string
    {
        return $this->city;
    }
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
    public function getProvince(): ?string
    {
        return $this->province;
    }
    public function setProvince(string $province): self
    {
        $this->province = $province;

        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
