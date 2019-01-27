<?php

namespace App\Repository;

use App\Entity\Outsider;
use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Outsider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outsider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outsider[]    findAll()
 * @method Outsider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutsiderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Outsider::class);
    }

//    /**
//     * @return Outsider[] Returns an array of Outsider objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByRegistration(Registration $registration)
    {
        return $this->createQueryBuilder('o')
            ->join('o.team', 't')
            ->join('t.race','r')
            ->andWhere('r.date >= :dstart')
            ->andWhere('r.date <= :dend')
            ->andWhere('o.number like :val')
            ->setParameter('dend', date_create_from_format('d/m/Y H:i:s','31/12/'.$registration->getDate()->format('Y').'29:59:59'))//todo: date end taking acompte of licence longue
            ->setParameter('dstart', $registration->getDate())
            ->setParameter('val', substr($registration->getNumber(),0,6).'%')
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Outsider
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
