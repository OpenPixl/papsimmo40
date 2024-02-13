<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Photo $entity, bool $flush = true): void
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
    public function remove(Photo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function firstphoto($idproperty): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.property = :property')
            ->orderBy('p.position', 'ASC')
            ->setParameter('property', $idproperty)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function lastphoto($idproperty): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.property = :property')
            ->orderBy('p.position', 'DESC')
            ->setParameter('property', $idproperty)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findNameBy($id){
        return $this->createQueryBuilder('p')
            ->select('p.galeryFrontName, p.path, p.createdAt')
            ->andWhere('p.property = :property')
            ->orderBy('p.position', 'ASC')
            ->setParameter('property', $id)
            ->getQuery()
            ->getResult()
            ;
    }


    // /**
    //  * @return Photo[] Returns an array of Photo objects
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
    public function findOneBySomeField($value): ?Photo
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
