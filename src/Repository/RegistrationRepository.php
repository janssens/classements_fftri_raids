<?php

namespace App\Repository;

use App\Entity\Athlete;
use App\Entity\PlannedTeam;
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

    public function findByString($value): ?array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.number like :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSameYear(Registration $registration): ?Registration
    {
        $year = $registration->getDate()->format('Y');
        $athlete = $registration->getAthlete();
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.end_date BETWEEN :date_start AND :date_end')
            ->andWhere('r.id != :id')
            ->andWhere('r.athlete = :athlete')
            ->setParameter('date_start', date_create_from_format('d/m/Y H:i:s','01/01/'.$year.' 00:00:00'))
            ->setParameter('date_end', date_create_from_format('d/m/Y H:i:s','01/01/'.($year+1).' 00:00:00'))
            ->setParameter('id', $registration->getId())
            ->setParameter('athlete', $athlete)
            ->setMaxResults(1)
            ->getQuery();
        return $query->getOneOrNullResult();
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

    public function getOneRequestByEmailAndPlannedTeam(string $email,PlannedTeam $plannedTeam): ?Registration
    {
        return $this->createQueryBuilder('r')
            ->join('r.planned_teams_requests', 'ptr')
            ->join('r.athlete', 'a')
            ->andWhere('ptr.id = :id')
            ->andWhere('a.email = :email')
            ->setParameter('id', $plannedTeam->getId())
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByLicenceAndRace($number,Race $race): ?Registration
    {
        $qb = $this->createQueryBuilder('r');
        return $qb->andWhere('r.number like :nb')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->lte('r.start_date', ':race_start'),
                    $qb->expr()->andX(
                        $qb->expr()->eq('YEAR(r.start_data)',':race_start_year'),
                        $qb->expr()->gt('MONTH(r.start_data)','8'),
                        $qb->expr()->eq('r.is_long','1')
                        )
                )
            )
            ->andWhere('r.end_date >= :race_start')
            ->setParameter('nb', $number.'%')
            ->setParameter('race_start', date_create_from_format('d/m/Y H:i:s',$race->getDate()->format('d/m/Y').' 00:00:00'))
            ->setParameter('race_start_year', date_create_from_format('Y',$race->getDate()->format('d/m/Y').' 00:00:00'))
            ->orderBy('r.date','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


}
