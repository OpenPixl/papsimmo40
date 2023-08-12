<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\BuildingEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuildingEquipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingEquipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingEquipment[]    findAll()
 * @method BuildingEquipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildingEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingEquipment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BuildingEquipment $entity, bool $flush = true): void
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
    public function remove(BuildingEquipment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return BuildingEquipment[] Returns an array of BuildingEquipment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BuildingEquipment
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
