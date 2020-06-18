<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="view_official_unisex_ranking")
 * @ORM\Entity(readOnly=true)
 */
class UnisexRanking
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Championship", inversedBy="rankings")
     */
    private $championship;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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
