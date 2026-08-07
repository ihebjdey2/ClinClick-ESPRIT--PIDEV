<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 *
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function save(Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countLowStock(int $threshold = 5): int
    {
        return (int) $this->createQueryBuilder('stock')
            ->select('COUNT(stock.id)')
            ->andWhere('(stock.quantiteDisponible IS NOT NULL AND stock.quantiteDisponible <= :threshold) OR (stock.quantiteDisponible IS NULL AND stock.quantite <= :threshold)')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function createListQueryBuilder(string $search = '', string $kind = '', ?int $categoryId = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('stock')
            ->join('stock.categorie', 'category')->addSelect('category')
            ->orderBy('stock.produit', 'ASC');

        $search = trim($search);
        if ($search !== '') {
            $queryBuilder
                ->andWhere('stock.produit LIKE :search OR category.libelle LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($kind === 'medicine') {
            $queryBuilder->andWhere('stock.dateExpiration IS NOT NULL');
        } elseif ($kind === 'equipment') {
            $queryBuilder->andWhere('stock.dateExpiration IS NULL');
        }
        if ($categoryId !== null && $categoryId > 0) {
            $queryBuilder->andWhere('category.id = :categoryId')->setParameter('categoryId', $categoryId);
        }

        return $queryBuilder;
    }
}
