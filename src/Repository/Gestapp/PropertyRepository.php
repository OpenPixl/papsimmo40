<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Property $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Property $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function fivelastproperties()
    {
        return $this->createQueryBuilder('p')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->join('c.denomination', 'd')
            ->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.priceFai as priceFai,
                p.surfaceHome as surfaceHome,
                d.name as denomination,
                p.piece as piece,
                p.room as room,
                p.city as city,
                pd.name as propertyDefinition,
                c.banner as banner
            ')
            ->where('p.isIncreating = 0')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    public function listAllProperties()
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->join('c.denomination', 'd')
            ->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.priceFai as priceFai,
                p.surfaceHome as surfaceHome,
                d.name as denomination,
                p.piece as piece,
                p.room as room,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.createdAt,
                p.updatedAt,
                e.id as refEmployed,
                e.firstName as firstName,
                e.lastName as lastName,
                e.avatarName as avatarName,
                pd.name as propertyDefinition,
                c.banner
            ')
            ->where('p.isIncreating = 0')
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listPropertiesByEmployed($user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                e.id as refEmployed,
                e.firstName as firstName,
                e.lastName as lastName,
                e.avatarName as avatarName,
                p.piece as piece,
                p.room as room,
                pd.name as propertyDefinition,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.imageName as imageName,
                p.createdAt as createdAt,
                p.updatedAt as updatedAt
            ')
            ->where('e.id = :user')
            ->setParameter('user', $user)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function oneProperty($property)
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->join('c.denomination', 'd')
            ->join('c.propertyState', 'ps')
            ->join('c.propertyEnergy', 'pe')
            ->join('c.propertyOrientation', 'po')
            ->join('c.propertyEquipment', 'peq')
            ->join('p.propertyDefinition', 'pd')
            ->join('c.propertyTypology', 'pt')
            ->addSelect('
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.piece as piece,
                p.room as room,
                e.id as refEmployed,
                e.firstName as firstName,
                e.lastName as lastName,
                e.avatarName as avatarName,
                c.washroom as washroom,
                c.bathroom as bathroom,
                c.terrace as terrace,
                c.balcony as balcony,
                c.wc as wc,
                d.name as denomination,
                p.surfaceHome as surfaceHome,
                p.surfaceLand as surfaceLand,
                p.dpeEstimateEnergyDown,
                p.dpeEstimateEnergyUp,
                p.priceFai as priceFai,
                pd.name as propertyDefinition,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.diagDpe as diagDpe,
                p.diagGpe as diagGpe,
                ps.name as propertyState,
                pe.name as propertyEnergy,
                po.name as propertyOrientation,
                pt.name as propertyTypology,
                c.propertyTax as propertyTax,
                c.disponibility as disponibility,
                c.location as location,
                c.disponibilityAt as disponibilityAt,
                p.createdAt as createdAt,
                p.updatedAt as updatedAt
            ')
            ->andWhere('p.id = :property')
            ->setParameter('property', $property)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function AllProperties()
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                e.id as refEmployed,
                e.firstName as firstName,
                e.lastName as lastName,
                e.avatarName as avatarName,
                p.piece as piece,
                p.room as room,
                pd.name as propertyDefinition,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.createdAt as createdAt,
                p.updatedAt as updatedAt
            ')
            ->where('p.isIncreating = 0')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
