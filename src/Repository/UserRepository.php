<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function createAdminListQueryBuilder(?string $search = null, ?string $role = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('user')->orderBy('user.nom', 'ASC')->addOrderBy('user.prenom', 'ASC');
        $search = trim((string) $search);

        if ($search !== '') {
            $queryBuilder
                ->andWhere('user.email LIKE :search OR user.nom LIKE :search OR user.prenom LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        if ($role !== null && in_array($role, User::BUSINESS_ROLES, true)) {
            $queryBuilder
                ->andWhere('user.roles LIKE :role')
                ->setParameter('role', '%"'.$role.'"%');
        }

        return $queryBuilder;
    }

    public function createByBusinessRoleQueryBuilder(string $role): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :role')
            ->andWhere('user.isVerified = true')
            ->setParameter('role', '%"'.$role.'"%')
            ->orderBy('user.nom', 'ASC')
            ->addOrderBy('user.prenom', 'ASC');
    }

    public function countByBusinessRole(string $role): int
    {
        if (!in_array($role, User::BUSINESS_ROLES, true)) {
            return 0;
        }

        return (int) $this->createQueryBuilder('user')
            ->select('COUNT(user.id)')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
