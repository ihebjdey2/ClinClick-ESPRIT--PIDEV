<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire.')]
    #[Assert\Length(min: 2, max: 120)]
    private ?string $produit = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La quantité est obligatoire.')]
    #[Assert\Positive(message: 'La quantité doit être strictement positive.')]
    private ?int $quantite = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'La quantité disponible doit être positive ou nulle.')]
    #[Assert\LessThanOrEqual(propertyPath: 'quantite', message: 'La quantité disponible ne peut pas dépasser la quantité totale.')]
    private ?int $quantiteDisponible = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThan('today', message: "La date d'expiration doit être dans le futur.")]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner une catégorie.')]
    private ?Categorie $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?string
    {
        return $this->produit;
    }

    public function setProduit(string $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getQuantiteDisponible(): ?int
    {
        return $this->quantiteDisponible;
    }

    public function setQuantiteDisponible(?int $quantiteDisponible): self
    {
        $this->quantiteDisponible = $quantiteDisponible;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function __toString(): string
    {
        return $this->produit ?? '';
    }
}
