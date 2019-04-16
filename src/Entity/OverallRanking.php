<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="view_ranking")
 * @ORM\Entity(readOnly=true)
 */
class OverallRanking
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Racer")
     * @ORM\JoinColumn(name="racer_id", referencedColumnName="id")
     */
    private $racer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $outsider;

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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRacer(): ?Racer
    {
        return $this->racer;
    }

    public function getOutsider(): ?bool
    {
        return $this->outsider;
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


    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

}
