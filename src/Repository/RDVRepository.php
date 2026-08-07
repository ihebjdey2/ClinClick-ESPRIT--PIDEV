<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RDV;
use App\Entity\DoctorAvailability;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<RDV> */
final class RDVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RDV::class);
    }

    public function save(RDV $appointment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($appointment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RDV $appointment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($appointment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createScopedListQueryBuilder(
        User $user,
        ?string $search = null,
        ?AppointmentStatus $status = null,
        ?DateTimeImmutable $date = null
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('appointment')
            ->leftJoin('appointment.patient', 'patient')->addSelect('patient')
            ->leftJoin('appointment.doctor', 'doctor')->addSelect('doctor')
            ->leftJoin('appointment.category', 'category')->addSelect('category')
            ->orderBy('appointment.scheduledAt', 'ASC');

        $this->applyUserScope($queryBuilder, $user);

        $search = trim((string) $search);
        if ($search !== '') {
            $queryBuilder
                ->andWhere('appointment.nom LIKE :search OR patient.nom LIKE :search OR patient.prenom LIKE :search OR doctor.nom LIKE :search OR doctor.prenom LIKE :search OR category.nom LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($status !== null) {
            $queryBuilder->andWhere('appointment.status = :status')->setParameter('status', $status);
        }
        if ($date !== null) {
            $queryBuilder
                ->andWhere('appointment.scheduledAt >= :dateStart AND appointment.scheduledAt < :dateEnd')
                ->setParameter('dateStart', $date->setTime(0, 0))
                ->setParameter('dateEnd', $date->modify('+1 day')->setTime(0, 0));
        }

        return $queryBuilder;
    }

    public function countScoped(User $user, ?AppointmentStatus $status = null, ?DateTimeImmutable $date = null): int
    {
        $queryBuilder = $this->createQueryBuilder('appointment')->select('COUNT(appointment.id)');
        $this->applyUserScope($queryBuilder, $user);

        if ($status !== null) {
            $queryBuilder->andWhere('appointment.status = :status')->setParameter('status', $status->value);
        }
        if ($date !== null) {
            $queryBuilder
                ->andWhere('appointment.scheduledAt >= :dateStart AND appointment.scheduledAt < :dateEnd')
                ->setParameter('dateStart', $date->setTime(0, 0))
                ->setParameter('dateEnd', $date->modify('+1 day')->setTime(0, 0));
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /** @return RDV[] */
    public function findUpcomingScoped(User $user, int $limit = 5): array
    {
        $queryBuilder = $this->createQueryBuilder('appointment')
            ->leftJoin('appointment.patient', 'patient')->addSelect('patient')
            ->leftJoin('appointment.doctor', 'doctor')->addSelect('doctor')
            ->leftJoin('appointment.category', 'category')->addSelect('category')
            ->andWhere('appointment.scheduledAt >= :now')
            ->andWhere('appointment.status IN (:activeStatuses)')
            ->setParameter('now', new DateTimeImmutable())
            ->setParameter('activeStatuses', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value])
            ->orderBy('appointment.scheduledAt', 'ASC')
            ->setMaxResults($limit);
        $this->applyUserScope($queryBuilder, $user);

        return $queryBuilder->getQuery()->getResult();
    }

    /** @return RDV[] */
    public function findPotentialConflicts(RDV $appointment, bool $lock = false): array
    {
        $startsAt = $appointment->getScheduledAt();
        $endsAt = $appointment->getEndsAt();
        if ($startsAt === null || $endsAt === null || $appointment->getDoctor() === null || $appointment->getPatient() === null) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('candidate')
            ->andWhere('candidate.status IN (:activeStatuses)')
            ->andWhere('candidate.scheduledAt < :endsAt')
            ->andWhere('candidate.scheduledAt > :windowStart')
            ->andWhere('candidate.doctor = :doctor OR candidate.patient = :patient')
            ->setParameter('activeStatuses', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->setParameter('endsAt', $endsAt)
            ->setParameter('windowStart', $startsAt->modify('-60 minutes'))
            ->setParameter('doctor', $appointment->getDoctor())
            ->setParameter('patient', $appointment->getPatient());

        if ($appointment->getId() !== null) {
            $queryBuilder->andWhere('candidate.id != :appointmentId')->setParameter('appointmentId', $appointment->getId());
        }

        $query = $queryBuilder->getQuery();
        if ($lock) {
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }

        return $query->getResult();
    }

    public function hasFutureAppointmentCoveredBy(DoctorAvailability $availability): bool
    {
        if ($availability->getDoctor() === null) {
            return false;
        }

        $appointments = $this->createQueryBuilder('appointment')
            ->andWhere('appointment.doctor = :doctor')
            ->andWhere('appointment.scheduledAt > :now')
            ->andWhere('appointment.status IN (:activeStatuses)')
            ->setParameter('doctor', $availability->getDoctor())
            ->setParameter('now', new DateTimeImmutable())
            ->setParameter('activeStatuses', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value])
            ->getQuery()
            ->getResult();

        foreach ($appointments as $appointment) {
            if ($appointment instanceof RDV
                && $appointment->getScheduledAt() !== null
                && $appointment->getEndsAt() !== null
                && $availability->covers($appointment->getScheduledAt(), $appointment->getEndsAt())) {
                return true;
            }
        }

        return false;
    }

    private function applyUserScope(QueryBuilder $queryBuilder, User $user): void
    {
        $roles = $user->getRoles();
        if (in_array(User::ROLE_ADMIN, $roles, true) || in_array(User::ROLE_RECEPTIONIST, $roles, true)) {
            return;
        }

        if (in_array(User::ROLE_DOCTOR, $roles, true)) {
            $queryBuilder->andWhere('appointment.doctor = :currentUser');
        } elseif (in_array(User::ROLE_PATIENT, $roles, true)) {
            $queryBuilder->andWhere('appointment.patient = :currentUser');
        } else {
            $queryBuilder->andWhere('1 = 0');
        }
        $queryBuilder->setParameter('currentUser', $user);
    }
}
