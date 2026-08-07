<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use App\Entity\Consultation;
use App\Entity\Prescription;
use App\Entity\RDV;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use App\Exception\MedicalRecordRuleException;
use Doctrine\ORM\EntityManagerInterface;

final class ConsultationService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(RDV $appointment, Consultation $consultation, User $doctor): void
    {
        $this->assertAssignedDoctor($appointment, $doctor);
        if ($appointment->getStatus() !== AppointmentStatus::CONFIRMED) {
            throw new MedicalRecordRuleException('Une consultation ne peut être créée que pour un rendez-vous confirmé.');
        }
        if ($appointment->getScheduledAt() === null || $appointment->getScheduledAt() > new DateTimeImmutable()) {
            throw new MedicalRecordRuleException('La consultation ne peut pas être enregistrée avant le début du rendez-vous.');
        }
        if ($appointment->getConsultation() !== null) {
            throw new MedicalRecordRuleException('Une consultation existe déjà pour ce rendez-vous.');
        }

        $this->entityManager->wrapInTransaction(function () use ($appointment, $consultation): void {
            $appointment->setConsultation($consultation);
            $appointment->setStatus(AppointmentStatus::COMPLETED);
            $this->entityManager->persist($consultation);
            $this->entityManager->persist($appointment);
        });
    }

    public function update(Consultation $consultation, User $doctor): void
    {
        $appointment = $consultation->getAppointment();
        if (!$appointment instanceof RDV) {
            throw new MedicalRecordRuleException('Le rendez-vous associé est introuvable.');
        }

        $this->assertAssignedDoctor($appointment, $doctor);
        $this->entityManager->flush();
    }

    public function addPrescription(Consultation $consultation, Prescription $prescription, User $doctor): void
    {
        $appointment = $consultation->getAppointment();
        if (!$appointment instanceof RDV) {
            throw new MedicalRecordRuleException('Le rendez-vous associé est introuvable.');
        }

        $this->assertAssignedDoctor($appointment, $doctor);
        $prescription->setConsultation($consultation);
        $this->entityManager->persist($prescription);
        $this->entityManager->flush();
    }

    private function assertAssignedDoctor(RDV $appointment, User $doctor): void
    {
        if (!in_array(User::ROLE_DOCTOR, $doctor->getRoles(), true)
            || $appointment->getDoctor()?->getId() !== $doctor->getId()) {
            throw new MedicalRecordRuleException("Seul le médecin assigné au rendez-vous peut modifier ce dossier médical.");
        }
    }
}
