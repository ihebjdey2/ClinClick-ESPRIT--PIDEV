<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Prescription> */
final class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    public function save(Prescription $prescription, bool $flush = false): void
    {
        $this->getEntityManager()->persist($prescription);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
