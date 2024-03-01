<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\Cistomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cistomer>
 *
 * @method Cistomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cistomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cistomer[]    findAll()
 * @method Cistomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CistomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cistomer::class);
    }

//    /**
//     * @return Cistomer[] Returns an array of Cistomer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cistomer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
