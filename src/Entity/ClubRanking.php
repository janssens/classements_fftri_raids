<?php

namespace App\Entity;

use App\Repository\ClubRankingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="view_club_ranking")
 * @ORM\Entity(readOnly=true)
 */
class ClubRanking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Club::class, inversedBy="clubRankings")
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity=Championship::class, inversedBy="clubRankings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $championship;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

}
