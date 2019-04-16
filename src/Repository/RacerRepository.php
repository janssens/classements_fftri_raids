<?php

namespace App\Repository;

use App\Entity\Racer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Racer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Racer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Racer[]    findAll()
 * @method Racer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RacerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Racer::class);
    }

     /**
     * @return Racer[] Returns an array of Racer objects
     */

    public function findByString($value): ?array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('concat(r.firstname,r.lastname) like :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Race
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
