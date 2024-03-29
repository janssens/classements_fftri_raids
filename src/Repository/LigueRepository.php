<?php

namespace App\Repository;

use App\Entity\Ligue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ligue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ligue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ligue[]    findAll()
 * @method Ligue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ligue::class);
    }

//    /**
//     * @return Ligue[] Returns an array of Ligue objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneBySlug($slug): ?Ligue
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.slug = :val')
            ->setParameter('val', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
