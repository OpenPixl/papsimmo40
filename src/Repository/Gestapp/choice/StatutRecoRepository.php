<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\StatutReco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutReco>
 *
 * @method StatutReco|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutReco|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutReco[]    findAll()
 * @method StatutReco[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutRecoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutReco::class);
    }

//    /**
//     * @return StatutReco[] Returns an array of StatutReco objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatutReco
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
