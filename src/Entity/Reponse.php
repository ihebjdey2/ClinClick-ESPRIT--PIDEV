<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'objet est obligatoire.")]
    #[Assert\Length(max: 255)]
    private ?string $objet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $message = null;



    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: 'relation_reclamation_id', nullable: false)]
    private ?Reclamation $relationReclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = trim($objet);

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = trim($message);

        return $this;
    }



    public function getRelationReclamation(): ?Reclamation
    {
        return $this->relationReclamation;
    }

    public function setRelationReclamation(Reclamation $reclamation): self
    {
        $this->relationReclamation = $reclamation;

        return $this;
    }
}
