<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DoctorAvailability;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DoctorAvailability> */
final class DoctorAvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctorAvailability::class);
    }

    public function save(DoctorAvailability $availability, bool $flush = false): void
    {
        $this->getEntityManager()->persist($availability);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DoctorAvailability $availability, bool $flush = false): void
    {
        $this->getEntityManager()->remove($availability);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return DoctorAvailability[] */
    public function findActiveForDoctorAndDay(User $doctor, int $dayOfWeek): array
    {
        return $this->findBy(
            ['doctor' => $doctor, 'dayOfWeek' => $dayOfWeek, 'isActive' => true],
            ['startTime' => 'ASC']
        );
    }

    public function isDoctorAvailable(User $doctor, DateTimeImmutable $startsAt, DateTimeImmutable $endsAt): bool
    {
        foreach ($this->findActiveForDoctorAndDay($doctor, (int) $startsAt->format('N')) as $availability) {
            if ($availability->covers($startsAt, $endsAt)) {
                return true;
            }
        }

        return false;
    }

    public function hasOverlap(DoctorAvailability $availability): bool
    {
        if ($availability->getDoctor() === null || $availability->getStartTime() === null || $availability->getEndTime() === null) {
            return false;
        }

        $queryBuilder = $this->createQueryBuilder('candidate')
            ->select('COUNT(candidate.id)')
            ->andWhere('candidate.doctor = :doctor')
            ->andWhere('candidate.dayOfWeek = :day')
            ->andWhere('candidate.isActive = true')
            ->andWhere('candidate.startTime < :endTime AND candidate.endTime > :startTime')
            ->setParameter('doctor', $availability->getDoctor())
            ->setParameter('day', $availability->getDayOfWeek())
            ->setParameter('startTime', $availability->getStartTime())
            ->setParameter('endTime', $availability->getEndTime());

        if ($availability->getId() !== null) {
            $queryBuilder->andWhere('candidate.id != :availabilityId')->setParameter('availabilityId', $availability->getId());
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }
}
