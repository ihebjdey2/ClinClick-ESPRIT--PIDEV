<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Consultation> */
final class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function save(Consultation $consultation, bool $flush = false): void
    {
        $this->getEntityManager()->persist($consultation);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createScopedQueryBuilder(User $user): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('consultation')
            ->join('consultation.appointment', 'appointment')->addSelect('appointment')
            ->join('appointment.patient', 'patient')->addSelect('patient')
            ->join('appointment.doctor', 'doctor')->addSelect('doctor')
            ->leftJoin('consultation.prescriptions', 'prescription')->addSelect('prescription')
            ->orderBy('consultation.consultedAt', 'DESC');

        if (in_array(User::ROLE_DOCTOR, $user->getRoles(), true)) {
            $queryBuilder->andWhere('appointment.doctor = :user');
        } elseif (in_array(User::ROLE_PATIENT, $user->getRoles(), true)) {
            $queryBuilder->andWhere('appointment.patient = :user');
        } else {
            $queryBuilder->andWhere('1 = 0');
        }

        return $queryBuilder->setParameter('user', $user);
    }
}
