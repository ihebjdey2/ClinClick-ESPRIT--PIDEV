<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RDV;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use App\Exception\AppointmentRuleException;
use App\Repository\DoctorAvailabilityRepository;
use App\Repository\RDVRepository;
use DateTimeImmutable;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class AppointmentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RDVRepository $appointmentRepository,
        private readonly DoctorAvailabilityRepository $availabilityRepository
    ) {
    }

    public function save(RDV $appointment): void
    {
        $this->validateBusinessRules($appointment);

        $this->entityManager->wrapInTransaction(function () use ($appointment): void {
            $doctor = $appointment->getDoctor();
            $patient = $appointment->getPatient();
            if ($doctor instanceof User) {
                $this->entityManager->lock($doctor, LockMode::PESSIMISTIC_WRITE);
            }
            if ($patient instanceof User) {
                $this->entityManager->lock($patient, LockMode::PESSIMISTIC_WRITE);
            }

            foreach ($this->appointmentRepository->findPotentialConflicts($appointment, true) as $candidate) {
                if (!$candidate instanceof RDV || $candidate->getEndsAt() === null || $appointment->getScheduledAt() === null) {
                    continue;
                }
                if ($candidate->getEndsAt() <= $appointment->getScheduledAt()) {
                    continue;
                }

                if ($candidate->getDoctor()?->getId() === $appointment->getDoctor()?->getId()) {
                    throw new AppointmentRuleException('Ce médecin possède déjà un rendez-vous sur ce créneau.');
                }
                if ($candidate->getPatient()?->getId() === $appointment->getPatient()?->getId()) {
                    throw new AppointmentRuleException('Ce patient possède déjà un rendez-vous sur ce créneau.');
                }
            }

            $this->entityManager->persist($appointment);
        });
    }

    public function changeStatus(RDV $appointment, AppointmentStatus $newStatus, User $actor): void
    {
        $currentStatus = $appointment->getStatus();
        if ($currentStatus === $newStatus) {
            return;
        }

        $allowedTransitions = [
            AppointmentStatus::PENDING->value => [AppointmentStatus::CONFIRMED, AppointmentStatus::CANCELLED],
            AppointmentStatus::CONFIRMED->value => [AppointmentStatus::COMPLETED, AppointmentStatus::CANCELLED, AppointmentStatus::NO_SHOW],
            AppointmentStatus::COMPLETED->value => [],
            AppointmentStatus::CANCELLED->value => [],
            AppointmentStatus::NO_SHOW->value => [],
        ];
        if (!in_array($newStatus, $allowedTransitions[$currentStatus->value], true)) {
            throw new AppointmentRuleException('Cette transition de statut est interdite.');
        }

        if ($newStatus === AppointmentStatus::COMPLETED) {
            throw new AppointmentRuleException('Le rendez-vous est terminé automatiquement lors de l’enregistrement de la consultation.');
        }

        $roles = $actor->getRoles();
        if (in_array(User::ROLE_PATIENT, $roles, true) && !in_array(User::ROLE_ADMIN, $roles, true)) {
            if ($newStatus !== AppointmentStatus::CANCELLED || $appointment->getPatient()?->getId() !== $actor->getId()) {
                throw new AppointmentRuleException("Un patient peut uniquement annuler l'un de ses rendez-vous.");
            }
            if ($appointment->getScheduledAt() !== null && $appointment->getScheduledAt() <= new DateTimeImmutable()) {
                throw new AppointmentRuleException("Un rendez-vous passé ne peut plus être annulé par le patient.");
            }
        }
        if (in_array(User::ROLE_DOCTOR, $roles, true) && !in_array(User::ROLE_ADMIN, $roles, true)) {
            if ($appointment->getDoctor()?->getId() !== $actor->getId()
                || !in_array($newStatus, [AppointmentStatus::CONFIRMED, AppointmentStatus::NO_SHOW], true)) {
                throw new AppointmentRuleException("Ce changement de statut n'est pas autorisé pour ce médecin.");
            }
        }
        if (in_array(User::ROLE_RECEPTIONIST, $roles, true) && !in_array(User::ROLE_ADMIN, $roles, true)
            && !in_array($newStatus, [AppointmentStatus::CONFIRMED, AppointmentStatus::CANCELLED], true)) {
            throw new AppointmentRuleException("Ce changement de statut n'est pas autorisé pour la réception.");
        }

        $appointment->setStatus($newStatus);
        $this->appointmentRepository->save($appointment, true);
    }

    private function validateBusinessRules(RDV $appointment): void
    {
        $startsAt = $appointment->getScheduledAt();
        $endsAt = $appointment->getEndsAt();
        $patient = $appointment->getPatient();
        $doctor = $appointment->getDoctor();

        if ($startsAt === null || $endsAt === null || $startsAt <= new DateTimeImmutable()) {
            throw new AppointmentRuleException('Le rendez-vous doit être planifié dans le futur.');
        }
        if (!$patient instanceof User || !in_array(User::ROLE_PATIENT, $patient->getRoles(), true)) {
            throw new AppointmentRuleException('Le compte sélectionné ne possède pas le rôle patient.');
        }
        if (!$doctor instanceof User || !in_array(User::ROLE_DOCTOR, $doctor->getRoles(), true)) {
            throw new AppointmentRuleException('Le compte sélectionné ne possède pas le rôle médecin.');
        }
        if (!$this->availabilityRepository->isDoctorAvailable($doctor, $startsAt, $endsAt)) {
            throw new AppointmentRuleException("Ce créneau ne correspond pas aux disponibilités du médecin.");
        }
    }
}
