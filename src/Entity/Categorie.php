<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_stock_category_name', columns: ['libelle'])]
#[UniqueEntity(fields: ['libelle'], message: 'Une catégorie de stock porte déjà ce libellé.')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire.')]
    #[Assert\Length(min: 3, max: 80)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Stock::class, orphanRemoval: true)]
    private Collection $stocks;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = trim($libelle);

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStocks(Stock $stocks): self
    {
        if (!$this->stocks->contains($stocks)) {
            $this->stocks->add($stocks);
            $stocks->setCategorie($this);
        }

        return $this;
    }

    public function removeStocks(Stock $stocks): self
    {
        if ($this->stocks->removeElement($stocks)) {
            // set the owning side to null (unless already changed)
            if ($stocks->getCategorie() === $this) {
                $stocks->setCategorie(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle ?? '';
    }

}
