<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyDefinition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertyDefinition|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyDefinition|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyDefinition[]    findAll()
 * @method PropertyDefinition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyDefinitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyDefinition::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PropertyDefinition $entity, bool $flush = true): void
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
    public function remove(PropertyDefinition $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return propertyDefinition[] Returns an array of propertyDefinition objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?propertyDefinition
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}