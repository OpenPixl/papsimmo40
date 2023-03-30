<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyRubricss;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<propertyRubricss>
 *
 * @method propertyRubricss|null find($id, $lockMode = null, $lockVersion = null)
 * @method propertyRubricss|null findOneBy(array $criteria, array $orderBy = null)
 * @method propertyRubricss[]    findAll()
 * @method propertyRubricss[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class propertyRubricssRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, propertyRubricss::class);
    }

    public function add(propertyRubricss $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(propertyRubricss $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return propertyRubricss[] Returns an array of propertyRubricss objects
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

//    public function findOneBySomeField($value): ?propertyRubricss
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
