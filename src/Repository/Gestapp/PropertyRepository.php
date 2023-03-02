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
            ->join('p.refEmployed', 'e')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->leftJoin('c.banner', 'b')
            ->join('p.propertyDefinition', 'pd')
            ->leftJoin('c.denomination', 'd')
            ->leftJoin('p.publication', 'pu')
            ->addSelect('
                p.isArchived as isArchived,
                pu.isWebpublish as isWebpublish,
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
                b.name as banner,
                b.bannerFilename AS bannerFilename
            ')
            ->where('p.isIncreating = 0')
            ->andWhere('p.isArchived = 0')
            ->andWhere('pu.isWebpublish = 1')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function listAllProperties()
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->leftJoin('c.banner', 'b')
            ->join('p.propertyDefinition', 'pd')
            ->leftJoin('c.denomination', 'd')
            ->addSelect('
                p.isArchived as isArchived,
                d.name as denomination,
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.priceFai as priceFai,
                p.surfaceHome as surfaceHome,
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
                b.name AS banner,
                b.bannerFilename AS bannerFilename

            ')
            ->where('p.isIncreating = 0')
            ->andWhere('p.isArchived = 0')
            ->orderBy('p.RefMandat', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listAllPropertiesArchived()
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->leftJoin('c.banner', 'b')
            ->join('p.propertyDefinition', 'pd')
            ->leftJoin('c.denomination', 'd')
            ->addSelect('
                p.isArchived as isArchived,
                d.name as denomination,
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.priceFai as priceFai,
                p.surfaceHome as surfaceHome,
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
                b.name AS banner,
                b.bannerFilename AS bannerFilename

            ')
            ->where('p.isIncreating = 0')
            ->andWhere('p.isArchived = 1')
            ->orderBy('p.RefMandat', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listAllPropertiesIncreating()
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            //->join('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            //->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.isArchived as isArchived,
                e.id as refEmployed,
                e.firstName as firstName,
                e.lastName as lastName,
                e.avatarName as avatarName,
                p.id as id,
                p.ref as ref,
                p.RefMandat as refMandat,
                p.name as name,
                p.annonce as annonce,
                p.priceFai as priceFai,
                p.surfaceHome as surfaceHome,
                p.piece as piece,
                p.room as room,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.createdAt,
                p.updatedAt
            ')
        ->where('p.isIncreating = 1')
        ->andWhere('p.isArchived = 0')
        ->orderBy('p.RefMandat', 'DESC')
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
                p.isArchived as isArchived,
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
            ->where('e.id = :user')
            ->andWhere('p.isArchived = 0')
            ->setParameter('user', $user)
            ->orderBy('p.RefMandat', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listPropertiesByEmployedIncreating($user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.refEmployed', 'e')
            ->join('p.propertyDefinition', 'pd')
            ->addSelect('
                p.isArchived as isArchived,
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
            ->where('e.id = :user')
            ->andWhere('p.isIncreating = 1')
            ->andWhere('p.isArchived = 0')
            ->setParameter('user', $user)
            ->orderBy('p.RefMandat', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function oneProperty($property)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.refEmployed', 'e')
            ->leftJoin('p.options', 'c')    // p.options correspond à la table "Complement" d'où l'alias "c"
            ->leftJoin('c.banner', 'b')
            ->leftJoin('c.propertyState', 'ps')
            ->leftJoin('c.propertyEnergy', 'pe')
            ->leftJoin('c.propertyOrientation', 'po')
            ->leftJoin('c.propertyEquipment', 'peq')
            ->leftJoin('p.propertyDefinition', 'pd')
            ->leftJoin('c.propertyTypology', 'pt')
            ->leftJoin('c.denomination', 'd')
            ->addSelect('
                p.isArchived as isArchived,
                d.name as denomination,
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
                c.isFurnished as isFurnished,
                p.surfaceHome as surfaceHome,
                p.surfaceLand as surfaceLand,
                p.dpeEstimateEnergyDown,
                p.dpeEstimateEnergyUp,
                p.eeaYear as anneeref,
                p.priceFai as priceFai,
                pd.name as propertyDefinition,
                p.adress as adress,
                p.complement as complement,
                p.zipcode as zipcode,
                p.city as city,
                p.diagChoice as diagChoice,
                p.diagDpe as diagDpe,
                p.diagGes as diagGes,
                ps.name as propertyState,
                pe.name as propertyEnergy,
                po.name as propertyOrientation,
                pt.name as propertyTypology,
                c.propertyTax as propertyTax,
                c.disponibility as disponibility,
                c.location as location,
                c.disponibilityAt as disponibilityAt,
                c.level as level,
                p.createdAt as createdAt,
                p.updatedAt as updatedAt
            ')
            ->andWhere('p.id = :property')
            ->andWhere('p.isArchived = 0')
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
                p.isArchived as isArchived,
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
            ->andWhere('p.isArchived = 0')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Rechercher un bien depuis le searchProperty
     * @return void
     */
    public function SearchPropertyHome($keys)
    {
        $query = $this->createQueryBuilder('p');
        $query->join('p.refEmployed', 'e');
        $query->join('p.options', 'c'); // p.options correspond à la table "Complement" d'où l'alias "c"
        $query->leftjoin('c.banner', 'b');
        $query->join('c.denomination', 'd');
        $query->join('p.propertyDefinition', 'pd');
        $query->join('p.publication', 'pu');
        $query->addSelect('
                p.isArchived as isArchived,
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
                b.name AS banner,
                b.bannerFilename AS bannerFilename 
        ');
        $query->where('p.isIncreating = 0');
        $query->andWhere('pu.isWebpublish = 1');
        $query->andWhere('p.isArchived = 0');

        if($keys != null){
            $query
                ->andWhere('MATCH_AGAINST(p.ref, p.name, p.zipcode, p.city) AGAINST (:keys boolean)>0')
                ->setParameter('keys', $keys);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * Recherche complete sur les biens
     */
    public function searchPropertyAll($keys, $priceMin, $priceMax){
        $query = $this->createQueryBuilder('p');
        $query->join('p.refEmployed', 'e');
        $query->join('p.options', 'c'); // p.options correspond à la table "Complement" d'où l'alias "c"
        $query->join('c.denomination', 'd');
        $query->join('p.propertyDefinition', 'pd');
        $query->join('p.publication', 'pu');
        $query->where('p.isArchived = 0');
        $query->select('
                p.isArchived as isArchived,
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
                ');
        $query->where('p.isIncreating = 0');
        if($keys != null){
            $query
                ->andWhere('MATCH_AGAINST(p.ref, p.name, p.zipcode, p.city) AGAINST (:keys boolean)>0')
                ->setParameter('keys', $keys);

            $query->where('p.isWebpublish = 1');
        }
        return $query->getQuery()->getResult();
    }

    public function reportpropertycsv()
    {
        $query = $this->createQueryBuilder('p');
        $query->join('p.refEmployed', 'e');
        $query->join('p.options', 'c');                     // p.options correspond à la table "Complement" d'où l'alias "c"
        $query->leftjoin('c.denomination', 'd');
        $query->join('p.propertyDefinition', 'pd');
        $query->join('p.publication', 'pu');
        $query->leftjoin('p.sscategory', 'ss');
        $query->leftjoin('c.propertyOrientation', 'po');
        $query->leftjoin('c.propertyEnergy', 'pe');
        $query->where('pu.isPublishParven = 1');            // filtre sur la publication Paru-Vendu
        $query->andWhere('p.isArchived = 0');
        $query->select('
                c.wc as wc,
                c.washroom AS washroom,
                c.sanitation as sanitation,
                p.eeaYear AS RefDPE,
                p.diagChoice AS diagChoice,                
                c.coproprietyTaxe as chargeCopro,
                c.coproperty as copro,
                pe.name AS energy,
                pe.slCode AS slCode,
                c.disponibilityAt as disponibilityAt,
                po.name AS orientation,
                p.mandatAt as mandatAt,
                p.isArchived as isArchived,
                pd.code as propertyCode,
                pd.name as propertyDefinition,
                ss.code as ssCategory,
                c.id AS idComplement,
                c.bathroom as bathroom,
                c.balcony as balcony,
                c.terrace as terrace,
                c.isFurnished as isFurnished,
                c.level as level,
                c.coproperty as coproperty,
                e.email as email,
                e.gsm as gsm,
                e.firstName as firstName,
                e.lastName AS lastName,
                p.projet as projet,
                p.isWithExclusivity as isWithExclusivity,
                p.price,
                p.ref AS ref,
                p.dpeAt as dpeAt, 
                p.dpeEstimateEnergyDown as dpeEstimateEnergyDown,
                p.dpeEstimateEnergyUp as dpeEstimateEnergyUp,
                p.constructionAt as constructionAt,
                p.piece as piece,
                p.room as room,
                p.surfaceLand as surfaceLand,
                p.surfaceHome as surfaceHome,
                p.priceFai,
                p.diagGes as diagGes,
                p.diagDpe as diagDpe,
                p.zipcode as zipcode,
                p.city as city,
                p.adress as adress,
                p.annonce as annonce,
                p.name as name,
                p.RefMandat as refMandat,
                p.id as id
            ');
        return $query->getQuery()->getResult();
    }

    public function reportpropertycsv2()
    {
        $query = $this->createQueryBuilder('p');
        $query->join('p.refEmployed', 'e');
        $query->join('p.options', 'c');                     // p.options correspond à la table "Complement" d'où l'alias "c"
        $query->leftjoin('c.denomination', 'd');
        $query->join('p.propertyDefinition', 'pd');
        $query->join('p.publication', 'pu');
        $query->leftjoin('p.sscategory', 'ss');
        $query->leftjoin('c.propertyOrientation', 'po');
        $query->leftjoin('c.propertyEnergy', 'pe');
        $query->where('pu.isPublishMeilleur = 1 OR pu.isPublishleboncoin = 1');            // filtre sur la publication Paru-Vendu
        $query->andWhere('p.isArchived = 0');
        $query->select('
                pu.isPublishleboncoin AS leboncoin,
                pu.isPublishMeilleur AS seloger,
                c.wc as wc,
                c.washroom AS washroom,
                c.sanitation as sanitation,
                p.eeaYear AS RefDPE,
                p.diagChoice AS diagChoice,                
                c.coproprietyTaxe as chargeCopro,
                c.coproperty as copro,
                pe.name AS energy,
                pe.slCode AS slCode,
                c.disponibilityAt as disponibilityAt,
                po.name AS orientation,
                p.mandatAt as mandatAt,
                p.isArchived as isArchived,
                pd.code as propertyCode,
                pd.name as propertyDefinition,
                ss.code as ssCategory,
                c.id AS idComplement,
                c.bathroom as bathroom,
                c.balcony as balcony,
                c.terrace as terrace,
                c.isFurnished as isFurnished,
                c.level as level,
                c.coproperty as coproperty,
                e.email as email,
                e.gsm as gsm,
                e.firstName as firstName,
                e.lastName AS lastName,
                p.projet as projet,
                p.isWithExclusivity as isWithExclusivity,
                p.price,
                p.ref AS ref,
                p.dpeAt as dpeAt, 
                p.dpeEstimateEnergyDown as dpeEstimateEnergyDown,
                p.dpeEstimateEnergyUp as dpeEstimateEnergyUp,
                p.constructionAt as constructionAt,
                p.piece as piece,
                p.room as room,
                p.surfaceLand as surfaceLand,
                p.surfaceHome as surfaceHome,
                p.priceFai,
                p.diagGes as diagGes,
                p.diagDpe as diagDpe,
                p.zipcode as zipcode,
                p.city as city,
                p.adress as adress,
                p.annonce as annonce,
                p.name as name,
                p.RefMandat as refMandat,
                p.id as id
            ');
        return $query->getQuery()->getResult();
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
