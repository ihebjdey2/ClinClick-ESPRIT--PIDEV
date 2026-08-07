<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategorieReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CategorieReclamationRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_complaint_category_name', columns: ['nom'])]
#[UniqueEntity(fields: ['nom'], message: 'Une catégorie de réclamation porte déjà ce nom.')]
class CategorieReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 3, max: 80)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'categorieReclamation', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->nom ?? '';
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

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setCategorieReclamation($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getCategorieReclamation() === $this) {
                $reclamation->setCategorieReclamation(null);
            }
        }

        return $this;
    }

}
