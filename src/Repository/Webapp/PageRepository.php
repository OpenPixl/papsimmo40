<?php

namespace App\Repository\Webapp;

use App\Entity\Webapp\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Page $entity, bool $flush = true): void
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
    public function remove(Page $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Liste les pages qui s'afficheront dans le bloc menu.
     */
    public function listMenu()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.slug, p.state, p.isMenu, pa.id AS parent, p.position as position')
            ->leftJoin('p.parent', 'pa')
            ->andWhere('p.state = :state')
            ->andWhere('p.isMenu = :isMenu')
            ->setParameter('state', 'publiée')
            ->setParameter('isMenu', 1)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Liste la page qui s'affichera selon son slug.
     */
    public function findbyslug($slug)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.slug, p.description, p.state, p.isMenu, p.seoTitle, p.isShowtitle')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Page[] Returns an array of Page objects
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
    public function findOneBySomeField($value): ?Page
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
