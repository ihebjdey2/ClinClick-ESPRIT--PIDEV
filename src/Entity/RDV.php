<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AppointmentStatus;
use App\Repository\RDVRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RDVRepository::class)]
#[ORM\Table(name: 'rdv')]
#[ORM\Index(columns: ['doctor_id', 'scheduled_at', 'status'], name: 'idx_rdv_doctor_schedule')]
#[ORM\Index(columns: ['patient_id', 'scheduled_at'], name: 'idx_rdv_patient_schedule')]
#[ORM\HasLifecycleCallbacks]
class RDV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le motif du rendez-vous est obligatoire.')]
    #[Assert\Length(min: 3, max: 160)]
    private ?string $nom = null;

    /** Colonne historique conservée temporairement pour une migration sans perte. */
    #[ORM\Column(name: 'date_r', type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $legacyDate = null;

    /** Identifiant historique conservé uniquement pour la compatibilité des anciennes données. */
    #[ORM\Column(name: 'idpatient', nullable: true)]
    private ?int $legacyPatientId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'patient_id', nullable: true, onDelete: 'RESTRICT')]
    #[Assert\NotNull(message: 'Le patient est obligatoire.')]
    private ?User $patient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'doctor_id', nullable: true, onDelete: 'RESTRICT')]
    #[Assert\NotNull(message: 'Le médecin est obligatoire.')]
    private ?User $doctor = null;

    #[ORM\Column(name: 'scheduled_at', type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull(message: "La date et l'heure sont obligatoires.")]
    #[Assert\GreaterThan('now', message: 'Le rendez-vous doit être planifié dans le futur.')]
    private ?DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(name: 'duration_minutes', type: Types::SMALLINT)]
    #[Assert\Choice(choices: [15, 30, 45, 60], message: 'La durée sélectionnée est invalide.')]
    private int $durationMinutes = 30;

    #[ORM\Column(length: 20, enumType: AppointmentStatus::class)]
    private AppointmentStatus $status = AppointmentStatus::PENDING;

    #[ORM\ManyToOne(inversedBy: 'rDVs')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'RESTRICT')]
    #[Assert\NotNull(message: 'La catégorie est obligatoire.')]
    private ?CategoryR $category = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $notes = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToOne(mappedBy: 'appointment', targetEntity: Consultation::class)]
    private ?Consultation $consultation = null;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestampsAndLegacyFields(): void
    {
        $this->updatedAt = new DateTimeImmutable();
        if ($this->scheduledAt !== null) {
            $this->legacyDate = new DateTime($this->scheduledAt->format('Y-m-d'));
        }
        if ($this->patient !== null) {
            $this->legacyPatientId = $this->patient->getId();
        }
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

    public function getDateR(): ?DateTimeInterface
    {
        return $this->scheduledAt ?? $this->legacyDate;
    }

    public function getScheduledAt(): ?DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(DateTimeInterface $scheduledAt): self
    {
        $this->scheduledAt = DateTimeImmutable::createFromInterface($scheduledAt);

        return $this;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    public function getEndsAt(): ?DateTimeImmutable
    {
        return $this->scheduledAt?->modify(sprintf('+%d minutes', $this->durationMinutes));
    }

    public function getStatus(): AppointmentStatus
    {
        return $this->status;
    }

    public function setStatus(AppointmentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getCategory(): ?CategoryR
    {
        return $this->category;
    }

    public function setCategory(?CategoryR $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes === null || trim($notes) === '' ? null : trim($notes);

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

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        if ($consultation !== null && $consultation->getAppointment() !== $this) {
            $consultation->setAppointment($this);
        }

        return $this;
    }
}
