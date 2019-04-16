<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * @ORM\Table(name="view_racer")
 * @ORM\Entity(readOnly=true)
 * @ORM\Entity(repositoryClass="App\Repository\RacerRepository")
 */
class Racer
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Athlete", inversedBy="racer")
     */
    private $parent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $outsider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team")
     */
    private $team;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OverallRanking", mappedBy="racer")
     */
    private $overall_rankings;

    public function __construct()
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getParent(): ?Athlete
    {
        if ($this->outsider){
            return null;
        }
        return $this->parent;
    }

    public function getOutsider(): ?bool
    {
        return $this->outsider;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @return Collection|Ranking[]
     */
    public function getOverallRankings(): Collection
    {
        return $this->overall_rankings;
    }
}
