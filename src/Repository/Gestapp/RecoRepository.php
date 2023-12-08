<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\Reco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reco>
 *
 * @method Reco|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reco|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reco[]    findAll()
 * @method Reco[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reco::class);
    }

//    /**
//     * @return Reco[] Returns an array of Reco objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reco
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
