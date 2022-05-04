<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\CustomerChoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerChoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerChoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerChoice[]    findAll()
 * @method CustomerChoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerChoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerChoice::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CustomerChoice $entity, bool $flush = true): void
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
    public function remove(CustomerChoice $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CustomerChoice[] Returns an array of CustomerChoice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustomerChoice
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
