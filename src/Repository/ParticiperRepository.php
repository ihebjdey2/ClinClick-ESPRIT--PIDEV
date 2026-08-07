<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\Participer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participer>
 *
 * @method Participer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participer[]    findAll()
 * @method Participer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticiperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participer::class);
    }

    public function save(Participer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int[] $eventIds
     *
     * @return array<int, int>
     */
    public function countByEventIds(array $eventIds): array
    {
        if ($eventIds === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('participation')
            ->select('IDENTITY(participation.event) AS eventId, COUNT(participation.id) AS participantCount')
            ->andWhere('participation.event IN (:eventIds)')
            ->setParameter('eventIds', $eventIds)
            ->groupBy('participation.event')
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row['eventId']] = (int) $row['participantCount'];
        }

        return $counts;
    }

    public function countForEvent(Evenement $event): int
    {
        return $this->count(['event' => $event]);
    }

}
