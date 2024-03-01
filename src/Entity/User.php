<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: 'This email is already in use.')]
#[UniqueEntity('username', message: 'This username is already in use.')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_user", type: "integer")]
    private ?int $id_user = null;

    #[ORM\Column(type: "integer")]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;
       /**
     * @Assert\NotBlank(message=" username doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un username au mini de 5 caracteres"
     *
     *     )
     * @ORM\Column(type="string", length=255)
     */

    #[ORM\Column(length: 255)]
    private ?string $username = null;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Password cannot be empty")
     * @Assert\Length(
     *      min=8,
     *      minMessage="Password must be at least {{ limit }} characters long"
     * )
     * @Assert\Regex(
     *      pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/",
     *      message="Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character"
     * )
     */ 
    #[ORM\Column(length: 255)]
    private ?string $password = null;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Email address cannot be empty")
     * @Assert\Email(message="The email '{{ value }}' is not a valid email.")
     */
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
     private array $roles = [];

    public function getId_user(): ?int
    {
        return $this->id_user;
    }
   

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

 public function setRoles(array $roles = ['ROLE_USER']): self
{
    $this->roles = $roles;

    return $this;
}
}
