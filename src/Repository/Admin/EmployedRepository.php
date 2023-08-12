<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Employed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Employed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employed[]    findAll()
 * @method Employed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployedRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employed::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Employed $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Employed $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Employed) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function publishEmployedOnApp()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isVerified = :isVerified')
            ->andWhere('e.isWebpublish = :isWebpublish')
            ->setParameter('isVerified', 1)
            ->setParameter('isWebpublish', 1)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function selectEmployed()
    {
        return $this->createQueryBuilder('e')
            ->select('
                e.id as id,
                e.firstName as firstName,
                e.lastName as lastName
            ')
            ->andWhere('e.isVerified = :isVerified')
            ->setParameter('isVerified', 1)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Employed[] Returns an array of Employed objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Employed
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
