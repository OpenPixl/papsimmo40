<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Prescriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prescriber>
 *
 * @method Prescriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prescriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prescriber[]    findAll()
 * @method Prescriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescriber::class);
    }

//    /**
//     * @return Prescriber[] Returns an array of Prescriber objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Prescriber
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
