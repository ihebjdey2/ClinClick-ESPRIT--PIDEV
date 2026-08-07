<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctorAvailabilityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DoctorAvailabilityRepository::class)]
#[ORM\Table(name: 'doctor_availability')]
#[ORM\UniqueConstraint(name: 'uniq_doctor_availability_slot', columns: ['doctor_id', 'day_of_week', 'start_time', 'end_time'])]
#[ORM\Index(name: 'idx_availability_doctor_day', columns: ['doctor_id', 'day_of_week', 'is_active'])]
#[Assert\Expression(
    expression: 'this.getStartTime() < this.getEndTime()',
    message: "L'heure de fin doit être postérieure à l'heure de début."
)]
class DoctorAvailability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Le médecin est obligatoire.')]
    private ?User $doctor = null;

    #[ORM\Column(name: 'day_of_week', type: Types::SMALLINT)]
    #[Assert\Range(min: 1, max: 7)]
    private int $dayOfWeek = 1;

    #[ORM\Column(name: 'start_time', type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    private ?DateTimeImmutable $startTime = null;

    #[ORM\Column(name: 'end_time', type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    private ?DateTimeImmutable $endTime = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN)]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getDayLabel(): string
    {
        return [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'][$this->dayOfWeek] ?? 'Inconnu';
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function covers(DateTimeImmutable $startsAt, DateTimeImmutable $endsAt): bool
    {
        return $this->isActive
            && $this->dayOfWeek === (int) $startsAt->format('N')
            && $this->startTime !== null
            && $this->endTime !== null
            && $this->startTime->format('H:i:s') <= $startsAt->format('H:i:s')
            && $this->endTime->format('H:i:s') >= $endsAt->format('H:i:s');
    }
}
