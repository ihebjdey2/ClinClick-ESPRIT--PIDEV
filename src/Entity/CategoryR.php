<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_appointment_category_name', columns: ['nom'])]
#[UniqueEntity(fields: ['nom'], message: 'Un type de rendez-vous porte déjà ce nom.')]
class CategoryR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 3, max: 80)]

    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: RDV::class)]
    private Collection $rDVs;

    public function __construct()
    {
        $this->rDVs = new ArrayCollection();
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
     * @return Collection<int, RDV>
     */
    public function getRDVs(): Collection
    {
        return $this->rDVs;
    }

    public function addRDV(RDV $rDV): self
    {
        if (!$this->rDVs->contains($rDV)) {
            $this->rDVs->add($rDV);
            $rDV->setCategory($this);
        }

        return $this;
    }

    public function removeRDV(RDV $rDV): self
    {
        if ($this->rDVs->removeElement($rDV)) {
            // set the owning side to null (unless already changed)
            if ($rDV->getCategory() === $this) {
                $rDV->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
