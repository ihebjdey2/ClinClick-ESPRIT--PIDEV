<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DoctorAvailability;
use App\Entity\User;
use App\Exception\AvailabilityRuleException;
use App\Repository\DoctorAvailabilityRepository;
use App\Repository\RDVRepository;

final class DoctorAvailabilityService
{
    public function __construct(
        private readonly DoctorAvailabilityRepository $repository,
        private readonly RDVRepository $appointmentRepository
    ) {
    }

    public function save(DoctorAvailability $availability): void
    {
        $doctor = $availability->getDoctor();
        if (!$doctor instanceof User || !in_array(User::ROLE_DOCTOR, $doctor->getRoles(), true)) {
            throw new AvailabilityRuleException('Le compte sélectionné ne possède pas le rôle médecin.');
        }
        if ($availability->getStartTime() === null
            || $availability->getEndTime() === null
            || $availability->getStartTime() >= $availability->getEndTime()) {
            throw new AvailabilityRuleException("L'heure de fin doit être postérieure à l'heure de début.");
        }
        if ($availability->isActive() && $this->repository->hasOverlap($availability)) {
            throw new AvailabilityRuleException('Cette plage chevauche une disponibilité existante.');
        }

        $this->repository->save($availability, true);
    }

    public function remove(DoctorAvailability $availability): void
    {
        if ($this->appointmentRepository->hasFutureAppointmentCoveredBy($availability)) {
            throw new AvailabilityRuleException('Cette disponibilité couvre un rendez-vous futur et ne peut pas être supprimée.');
        }

        $this->repository->remove($availability, true);
    }
}
