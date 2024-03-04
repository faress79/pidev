<?php

namespace App\Entity;

use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AgenceRepository::class)]
class Agence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne doit pas être vide.')]
    #[Assert\Length(
        min: 4,
        minMessage: 'Le nom doit contenir au moins 8 caractères.'
    )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    private ?int $tel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'address ne doit pas être vide.')]
    #[Assert\Length(
        min: 8,
        minMessage: 'address de passe doit contenir au moins 8 caractères.'
    )]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\' email ne doit pas être vide.')]
    #[Assert\Length(
        min: 8,
        minMessage: 'L\' email doit contenir au moins 8 caractères.'
    )]
    #[Assert\Email]
    #[Assert\Length(max: 15)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: 'agence')]
    private Collection $agence;


    #[ORM\Column]
    private ?int $nbvue = 0;

    public function getNbVue(): ?int
    {
        return $this->nbvue;
    }

    public function setNbVue(?int $nbvue): self
    {
        $this->nbvue = $nbvue;

        return $this;
    }

    public function __construct()
    {
        $this->agence = new ArrayCollection();
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

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getAgence(): Collection
    {
        return $this->agence;
    }

    public function addAgence(Contrat $agence): static
    {
        if (!$this->agence->contains($agence)) {
            $this->agence->add($agence);
            $agence->setAgence($this);
        }

        return $this;
    }

    public function removeAgence(Contrat $agence): static
    {
        if ($this->agence->removeElement($agence)) {
            // set the owning side to null (unless already changed)
            if ($agence->getAgence() === $this) {
                $agence->setAgence(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
    #[ORM\Column]
    private ?int $likes = 0;
   

    #[ORM\Column]
    private ?int $dislikes = 0;

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): void
    {
        $this->dislikes = $dislikes;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }

    public function incrementDislikes(): void
    {
        $this->dislikes++;
    }
    public function checkAndDeleteIfRequired(): bool
    {
        return $this->dislikes - $this->likes >= 2;
    }

}
