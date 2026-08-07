<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse e-mail.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_DOCTOR = 'ROLE_DOCTOR';
    public const ROLE_RECEPTIONIST = 'ROLE_RECEPTIONIST';
    public const ROLE_PATIENT = 'ROLE_PATIENT';
    public const ROLE_USER = 'ROLE_USER';

    public const BUSINESS_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DOCTOR,
        self::ROLE_RECEPTIONIST,
        self::ROLE_PATIENT,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'adresse e-mail est obligatoire.")]
    #[Assert\Email(message: "L'adresse e-mail n'est pas valide.")]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    /** @var string[] */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 80)]
    #[Assert\Regex(pattern: "/^[\\p{L}\\p{M} '\\-]+$/u", message: 'Le nom contient des caractères non autorisés.')]
    private ?string $nom = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(min: 2, max: 80)]
    #[Assert\Regex(pattern: "/^[\\p{L}\\p{M} '\\-]+$/u", message: 'Le prénom contient des caractères non autorisés.')]
    private ?string $prenom = null;

    #[ORM\Column(name: 'date_naissance', type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de naissance est obligatoire.')]
    #[Assert\LessThan('today', message: 'La date de naissance doit être dans le passé.')]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['femme', 'homme', 'non_specifie'], message: 'Veuillez sélectionner une valeur valide.')]
    private ?string $genre = null;

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
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique(array_filter($roles, 'is_string')));

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = trim($prenom);

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getFullName(): string
    {
        return trim(($this->prenom ?? '').' '.($this->nom ?? ''));
    }

    public function __toString(): string
    {
        return $this->getFullName() !== '' ? $this->getFullName() : (string) $this->email;
    }
}
