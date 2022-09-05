<?php

namespace App\Repository;

use App\Entity\Club;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

//    /**
//     * @return Club[] Returns an array of Club objects
//     */
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

    /**
     * @return Club[] Returns an array of Club objects
     */
    public function findByString($value): ?array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name like :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneBySlug($slug): ?Club
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :val')
            ->setParameter('val', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getPointsByChampionshipId($championship_id,$club_id): ?int
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(r.points)')
            ->leftJoin('view_official_team_ranking','r','c.id = r.club')
            ->andWhere('r.championship_id = :championship_id')
            ->andWhere('c.id = :club_id')
            ->setParameter('championship_id', $championship_id)
            ->setParameter('club_id', $club_id)
            ->groupBy('CONCAT(c.id)')
            ->getQuery()
            ->getScalarResult()
            ;
    }

//``SELECT club as club_id, SUM(points) as points,championship_id
//FROM my_view_official_ranking
//WHERE championship_id = 3
//GROUP BY CONCAT(club);``

}
