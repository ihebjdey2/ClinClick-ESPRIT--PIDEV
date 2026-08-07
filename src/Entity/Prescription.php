<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrescriptionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: 'Le médicament est obligatoire.')]
    #[Assert\Length(min: 2, max: 160)]
    private ?string $medication = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le dosage est obligatoire.')]
    #[Assert\Length(max: 120)]
    private ?string $dosage = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: 'La fréquence est obligatoire.')]
    #[Assert\Length(max: 160)]
    private ?string $frequency = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $instructions = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startDate', message: 'La date de fin doit suivre la date de début.')]
    private ?DateTimeImmutable $endDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->startDate = new DateTimeImmutable('today');
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getMedication(): ?string
    {
        return $this->medication;
    }

    public function setMedication(string $medication): self
    {
        $this->medication = trim($medication);

        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(string $dosage): self
    {
        $this->dosage = trim($dosage);

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = trim($frequency);

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): self
    {
        $this->instructions = $instructions === null || trim($instructions) === '' ? null : trim($instructions);

        return $this;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
