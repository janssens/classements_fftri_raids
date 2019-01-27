<?php

namespace App\Repository;

use App\Entity\Race;
use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Registration::class);
    }

//    /**
//     * @return Registration[] Returns an array of Registration objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByDateAndNumber($date,$number): ?Registration
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date BETWEEN :date_start AND :date_end')
            ->andWhere('r.number = :nb')
            ->setParameter('date_start', date_create_from_format('d/m/Y H:i:s',$date->format('d/m/Y').' 00:00:00'))
            ->setParameter('date_end', date_create_from_format('d/m/Y H:i:s',$date->format('d/m/Y').' 23:59:59'))
            ->setParameter('nb', $number)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByLicence($number): ?Registration
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.number like :nb')
            ->setParameter('nb', $number.'%')
            ->orderBy('r.date','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByLicenceAndRace($number,Race $race): ?Registration
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.number like :nb')
            ->setParameter('nb', $number.'%')
            ->orderBy('r.date','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


}
