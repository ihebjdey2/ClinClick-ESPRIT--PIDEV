<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Evenement> */
final class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function save(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return Evenement[] */
    public function findUpcoming(int $limit = 20): array
    {
        return $this->createQueryBuilder('event')
            ->addSelect('category')
            ->join('event.category', 'category')
            ->andWhere('event.date >= :today')
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->orderBy('event.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countUpcoming(): int
    {
        return (int) $this->createQueryBuilder('event')
            ->select('COUNT(event.id)')
            ->andWhere('event.date >= :today')
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
