<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RankingRepository")
 * @ORM\Table(name="view_ranking")
 * @ORM\Entity(readOnly=true)
 */
class Ranking
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Athlete", inversedBy="rankings")
     * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id")
     */
    private $athlete;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Championship", inversedBy="rankings")
     */
    private $championship;


    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getCategoryHuman(): ?string
    {
        switch ($this->category){
            case OfficialTeam::GENDER_MALE:
                return 'Homme';
            case OfficialTeam::GENDER_FEMALE:
                return 'Femme';
            case OfficialTeam::GENDER_MIXED:
            default:
                return 'Mixte';
        }
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function getAthlete(): ?Athlete
    {
        return $this->athlete;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

}
