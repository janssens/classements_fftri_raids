<?php

namespace App\Repository;

use App\Entity\Athlete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Athlete|null find($id, $lockMode = null, $lockVersion = null)
 * @method Athlete|null findOneBy(array $criteria, array $orderBy = null)
 * @method Athlete[]    findAll()
 * @method Athlete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AthleteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Athlete::class);
    }

//    /**
//     * @return Athlete[] Returns an array of Athlete objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function findByString($value): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('concat(a.firstname,a.lastname) like :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByLastnameAndDob($lastname, $dob): ?Athlete
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.lastname = :val')
            ->andWhere('a.dob = :dob')
            ->setParameter('val', $lastname)
            ->setParameter('dob', $dob)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByFistnameLastnameAndDob($fistname, $lastname, $dob): ?Athlete
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.firstname = :valf')
            ->andWhere('a.lastname = :vall')
            ->andWhere('a.dob = :dob')
            ->setParameter('valf', $fistname)
            ->setParameter('vall', $lastname)
            ->setParameter('dob', $dob)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
