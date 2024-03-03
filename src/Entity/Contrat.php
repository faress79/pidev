<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date d\'effet ne doit pas être vide.')]
    private ?\DateTimeInterface $dateEffet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type de contrat ne doit pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le type de contrat ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $typeContrat = null;



    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix du contrat ne doit pas être vide.')]
    #[Assert\Type(type: 'integer', message: 'Le prix du contrat doit être un entier.')]
    #[Assert\Positive(message: 'Le prix du contrat doit être un nombre positif.')]
    private ?int $prixContrat = null;

    #[Assert\NotBlank(message: 'L\'agence du contrat ne doit pas être vide.')]
    #[ORM\ManyToOne(inversedBy: 'agence')]
    private ?Agence $agence = null;

    #[Assert\NotBlank(message: 'L4 UTILISATEUR ne doit pas être vide.')]
    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?User $utilisateur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEffet(): ?\DateTimeInterface
    {
        return $this->dateEffet;
    }

    public function setDateEffet(\DateTimeInterface $dateEffet): static
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): static
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): static
    {
        $this->agence = $agence;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getPrixContrat(): ?int
    {
        return $this->prixContrat;
    }

    public function setPrixContrat(int $prixContrat): static
    {
        $this->prixContrat = $prixContrat;

        return $this;
    }
}
