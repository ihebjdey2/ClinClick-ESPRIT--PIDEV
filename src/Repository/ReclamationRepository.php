<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function save(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createListQueryBuilder(?string $search = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('reclamation')
            ->leftJoin('reclamation.categorieReclamation', 'category')
            ->addSelect('category')
            ->orderBy('reclamation.id', 'DESC');

        $search = trim((string) $search);
        if ($search !== '') {
            $queryBuilder
                ->andWhere('reclamation.nom LIKE :search OR reclamation.email LIKE :search OR reclamation.description LIKE :search OR category.nom LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        return $queryBuilder;
    }

}
