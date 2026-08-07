<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 120)]
    #[Assert\Regex(
        pattern: "/^[\\p{L}\\p{M} '\\-]+$/u",
        message: 'Le nom contient des caractères non autorisés.'
    )]
    private ?string $nom = null;

    #[Assert\NotBlank(message: "L'adresse e-mail est obligatoire.")]
    #[Assert\Email(message: "L'adresse e-mail '{{ value }}' n'est pas valide.")]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 10, max: 500)]
    private ?string $description = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $etat = false;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner une catégorie.')]
    private ?CategorieReclamation $categorieReclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = trim($nom);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = trim($description);

        return $this;
    }

    public function isEtat(): bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCategorieReclamation(): ?CategorieReclamation
    {
        return $this->categorieReclamation;
    }

    public function setCategorieReclamation(?CategorieReclamation $categorieReclamation): self
    {
        $this->categorieReclamation = $categorieReclamation;

        return $this;
    }

}
