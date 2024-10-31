<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\AgencyEmployed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgencyEmployed>
 */
class AgencyEmployedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgencyEmployed::class);
    }

    public function listcollTransac($transaction){
        return $this->createQueryBuilder('a')
            ->leftJoin('a.refTransac', 't')
            ->select('
            t.id as idTransac,
            e.avatarName as avatarName,
            e.lastName as lastName,
            e.firstName as firstName,
            e.id as idEmployed,
            a.id as id
            ')
            ->andWhere('a.refTransac = :refTransac')
            ->setParameter('refTransac', $transaction)
            ->getQuery()
            ->getResult()
            ;
    }

    //    /**
    //     * @return AgencyEmployed[] Returns an array of AgencyEmployed objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AgencyEmployed
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
