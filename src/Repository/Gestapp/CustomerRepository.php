<?php

namespace App\Repository\Gestapp;

use App\Entity\Gestapp\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Customer $entity, bool $flush = true): void
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
    public function remove(Customer $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllCustomer(){
        return $this->createQueryBuilder('c')
            ->join('c.refEmployed', 'e')
            ->join('c.customerChoice', 'ch')
            ->select('
                e.id as refEmployed,
                e.firstName as firstNameEmpl,
                e.lastName as lastNameEmpl,
                e.avatarName as avatarName,
                c.isArchived AS isArchived,
                c.updatedAt AS updatedAt,
                c.createdAt AS CreatedAt,
                c.city AS city,
                c.zipcode AS zipcode,
                c.complement AS complement,
                c.adress AS adress,
                c.RefCustomer AS RefCustomer,
                c.firstName AS firstName,
                c.lastName AS lastName,
                c.id,
                ch.name as customerChoice
                                '
            )
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllCustomerByEmployed($user){
        return $this->createQueryBuilder('c')
            ->join('c.refEmployed', 'e')
            ->select('
                e.id as refEmployed,
                e.firstName as firstNameEmpl,
                e.lastName as lastNameEmpl,
                e.avatarName as avatarName,
                c.isArchived AS isArchived,
                c.updatedAt AS updatedAt,
                c.createdAt AS CreatedAt,
                c.city AS city,
                c.zipcode AS zipcode,
                c.complement AS complement,
                c.adress AS adress,
                c.RefCustomer AS RefCustomer,
                c.firstName AS firstName,
                c.lastName AS lastName,
                c.id
                '
            )
            ->where('e.id = :user')
            ->setParameter('user', $user)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listbyproperty($property)
    {
        return $this->createQueryBuilder('c')
            ->join('c.properties', 'p')
            ->andWhere('p.id = :property')
            ->setParameter('property', $property)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Rechercher un client depuis le search
     * @return void
     */
    public function SearchCustomers($keys)
    {
        $query = $this->createQueryBuilder('c');
        $query->where('c.isArchived = 0');
        if($keys != null){
            $query
                ->andWhere('MATCH_AGAINST(c.firstName, c.lastName) AGAINST (:keys boolean)>0')
                ->setParameter('keys', $keys);
        }
        return $query->getQuery()->getResult();
    }

    public function CustomerForProperty($property){
        return $this->createQueryBuilder('c')
            ->innerJoin("c.properties", "p")
            ->andWhere("p.id = :property")
            ->setParameter("property", $property)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
