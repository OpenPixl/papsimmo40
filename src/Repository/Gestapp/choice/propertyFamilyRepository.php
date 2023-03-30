<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<propertyFamily>
 *
 * @method propertyFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method propertyFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method propertyFamily[]    findAll()
 * @method propertyFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class propertyFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, propertyFamily::class);
    }

    public function add(propertyFamily $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(propertyFamily $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return propertyFamily[] Returns an array of propertyFamily objects
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

//    public function findOneBySomeField($value): ?propertyFamily
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
