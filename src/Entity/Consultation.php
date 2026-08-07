<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ConsultationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'consultation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RDV $appointment = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le diagnostic ou constat principal est obligatoire.')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $diagnosis = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Les notes cliniques sont obligatoires.')]
    #[Assert\Length(min: 10, max: 5000)]
    private ?string $clinicalNotes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('now', message: 'La date de consultation ne peut pas être dans le futur.')]
    private DateTimeImmutable $consultedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, Prescription> */
    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Prescription::class)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $prescriptions;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->consultedAt = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->prescriptions = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppointment(): ?RDV
    {
        return $this->appointment;
    }

    public function setAppointment(RDV $appointment): self
    {
        $this->appointment = $appointment;

        return $this;
    }

    public function getDiagnosis(): ?string
    {
        return $this->diagnosis;
    }

    public function setDiagnosis(string $diagnosis): self
    {
        $this->diagnosis = trim($diagnosis);

        return $this;
    }

    public function getClinicalNotes(): ?string
    {
        return $this->clinicalNotes;
    }

    public function setClinicalNotes(string $clinicalNotes): self
    {
        $this->clinicalNotes = trim($clinicalNotes);

        return $this;
    }

    public function getConsultedAt(): DateTimeImmutable
    {
        return $this->consultedAt;
    }

    public function setConsultedAt(DateTimeImmutable $consultedAt): self
    {
        $this->consultedAt = $consultedAt;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return Collection<int, Prescription> */
    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }
}
