<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryR>
 *
 * @method CategoryR|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryR|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryR[]    findAll()
 * @method CategoryR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryR::class);
    }

    public function save(CategoryR $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryR $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return CategoryR[]
     */
    public function findAllWithAppointments(): array
    {
        return $this->createQueryBuilder('category')
            ->leftJoin('category.rDVs', 'appointment')
            ->addSelect('appointment')
            ->orderBy('category.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
