<?php

namespace App\Repository\Gestapp\Transaction;

use App\Entity\Gestapp\Transaction\AddCollTransac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddCollTransac>
 *
 * @method AddCollTransac|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddCollTransac|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddCollTransac[]    findAll()
 * @method AddCollTransac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddCollTransacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddCollTransac::class);
    }

    public function listcollTransac($transaction){
        return $this->createQueryBuilder('a')
            ->leftJoin('a.refemployed', 'e')
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

    public function listcollEmployed($employed)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.refemployed', 'e')
            ->leftJoin('a.refTransac', 't')
            ->leftJoin('t.property', 'p')
            ->select('
                p.id as property,
                t.isValidPromisepdf as isValidPromisepdf,
                t.isValidActepdf as isValidActepdf,
                t.dateAtSale as dateAtSale,
                t.dateAtPromise as dateAtPromise,
                t.isClosedfolder as isClosedfolder,
                t.state as state,
                t.updatedAt as updatedAt,
                t.createdAt as createdAt,
                t.name as name,
                t.id as idTransac,
                e.id as refEmployed,
                e.avatarName as avatarName,
                e.lastName as lastName,
                e.firstName as firstName,
                a.id as id
            ')
            ->andWhere('a.refemployed = :refemployed')
            ->setParameter('refemployed', $employed)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return AddCollTransac[] Returns an array of AddCollTransac objects
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

//    public function findOneBySomeField($value): ?AddCollTransac
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
