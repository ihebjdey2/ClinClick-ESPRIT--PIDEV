<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategorieReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieReclamation>
 *
 * @method CategorieReclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieReclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieReclamation[]    findAll()
 * @method CategorieReclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieReclamation::class);
    }

    public function save(CategorieReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategorieReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
