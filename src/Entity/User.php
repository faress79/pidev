<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
<<<<<<< HEAD
    #[ORM\Column(name: "id_user", type: "integer")]
    private ?int $id_user = null;


    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;
    
  

   
=======
    #[ORM\Column]
    private ?int $id = null;
>>>>>>> agence

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: 'utilisateur')]
    private Collection $contrats;

<<<<<<< HEAD
    #[ORM\Column(length: 255)]
    private ?string $username = null;
    

    #[ORM\Column]
    private ?string $password = null;
    
    
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
     private array $roles = [];

    #[ORM\Column(type:'datetime_immutable',options:['default'=>'CURRENT_TIMESTAMP'])]
    private $created_at;
    #[ORM\Column(type: 'boolean')]
    private $is_verified = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $resetToken;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    // Setter for created_at
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id_user;
    }
    public function getId_user(): ?int
    {
        return $this->id_user;
    }
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->reclamations = new ArrayCollection();
=======
    public function __construct()
    {
        $this->contrats = new ArrayCollection();
>>>>>>> agence
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setUtilisateur($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getUtilisateur() === $this) {
                $contrat->setUtilisateur(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->nom;
    }
      /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }
    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setuser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getuser() === $this) {
                $reclamation->setuser(null);
            }
        }

        return $this;
    }
}
