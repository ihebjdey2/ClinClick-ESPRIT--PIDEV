<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_event_category_name', columns: ['nom'])]
#[UniqueEntity(fields: ['nom'], message: 'Une catégorie d’événement porte déjà ce nom.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 3, max: 80)]
    private ?string $nom = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire.')]
    #[Assert\Length(min: 3, max: 160)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Evenement::class)]
    private Collection $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = trim($libelle);

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom ?? '';
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setCategory($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getCategory() === $this) {
                $evenement->setCategory(null);
            }
        }

        return $this;
    }
}
