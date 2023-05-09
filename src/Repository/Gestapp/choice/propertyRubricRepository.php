<?php

namespace App\Repository\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyRubric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<propertyRubric>
 *
 * @method propertyRubric|null find($id, $lockMode = null, $lockVersion = null)
 * @method propertyRubric|null findOneBy(array $criteria, array $orderBy = null)
 * @method propertyRubric[]    findAll()
 * @method propertyRubric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class propertyRubricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, propertyRubric::class);
    }

    public function add(propertyRubric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(propertyRubric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function value(): array
    {
        return $this->createQueryBuilder('r')
            ->leftjoin('r.propertyFamily', 'f')
            ->select('r.id AS id, r.name AS name, f.id as family')
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listbyfamily($family): array
    {
        return $this->createQueryBuilder('r')
            ->leftjoin('r.propertyFamily', 'f')
            ->select('r.id AS id, r.name AS name')
            ->andWhere('f.id = :family')
            ->setParameter('family', $family)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return propertyRubric[] Returns an array of propertyRubric objects
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

//    public function findOneBySomeField($value): ?propertyRubric
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
