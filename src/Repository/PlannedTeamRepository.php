<?php

namespace App\Repository;

use App\Entity\Athlete;
use App\Entity\PlannedTeam;
use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlannedTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlannedTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlannedTeam[]    findAll()
 * @method PlannedTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlannedTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlannedTeam::class);
    }

    public function findOneConflict(PlannedTeam $plannedTeam){
        return $this->createQueryBuilder('t')
            ->join('t.race','r')
            ->leftJoin('t.requests','req')
            ->leftJoin('t.registrations','reg')
            ->andWhere('req.id IN (:registrations_id)')
            ->orWhere('reg.id IN (:registrations_id)')
            ->andWhere('r.id = :race_id')
            ->andWhere('t.id != :team_id')
            ->setParameter('team_id', $plannedTeam->getId())
            ->setParameter('race_id', $plannedTeam->getRace()->getId())
            ->setParameter('registrations_id', $plannedTeam->getRegistrationsIds())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    /**
//     * @return Team[] Returns an array of Team objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Team
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
