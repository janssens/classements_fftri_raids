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
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Athlete", inversedBy="rankings")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Season", inversedBy="rankings")
     */
    private $season;


    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    /**
     * @return Collection|Outsider[]
     */
    public function getOutsiders(): Collection
    {
        return $this->outsiders;
    }

    public function getNumberOfAthlete(): ?int
    {
        return $this->number_of_athlete;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
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


    public function getSeason(): ?Season
    {
        return $this->season;
    }

}
