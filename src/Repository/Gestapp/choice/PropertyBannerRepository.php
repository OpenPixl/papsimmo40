<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyBanner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PropertyBanner>
 *
 * @method PropertyBanner|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyBanner|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyBanner[]    findAll()
 * @method PropertyBanner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyBannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyBanner::class);
    }

    public function add(PropertyBanner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PropertyBanner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PropertyBanner[] Returns an array of PropertyBanner objects
     */
    public function listAll(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return PropertyBanner[] Returns an array of PropertyBanner objects
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

//    public function findOneBySomeField($value): ?PropertyBanner
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
