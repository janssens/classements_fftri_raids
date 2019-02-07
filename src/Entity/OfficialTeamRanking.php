<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficialTeamRankingRepository")
 * @ORM\Table(name="view_official_team_ranking")
 * @ORM\Entity(readOnly=true)
 */
class OfficialTeamRanking
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $overall_position;

    /**
     * @ORM\Column(type="integer")
     */
    private $category_position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Team", inversedBy="official_team_ranking")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="official_team_rankings")
     */
    private $race;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function getOverallPosition(): ?int
    {
        return $this->overall_position;
    }

    public function getCategoryPosition(): ?int
    {
        return $this->category_position;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }



}
