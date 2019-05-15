<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChampionshipRepository")
 */
class Championship
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $rank_outsider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Season", inversedBy="championships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $season;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Race", inversedBy="championships")
     */
    private $races;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Race", inversedBy="final_of")
     * @ORM\JoinColumn(name="final_id", referencedColumnName="id")
     */
    private $final;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ranking", mappedBy="championship")
     * @ORM\OrderBy({"points" = "DESC","athlete" = "DESC"})
     */
    private $rankings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OverallRanking", mappedBy="championship")
     * @ORM\OrderBy({"points" = "DESC","racer" = "DESC"})
     */
    private $overall_rankings;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registration_due_date;

    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRankOutsider(): ?bool
    {
        return $this->rank_outsider;
    }

    public function setRankOutsider(string $rank_outsider): self
    {
        $this->rank_outsider = $rank_outsider;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getFinal(): ?Race
    {
        return $this->final;
    }

    public function setFinal(?Race $race): self
    {
        $this->final = $race;

        return $this;
    }

    /**
     * @return Collection|Ranking[]
     */
    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    /**
     * @return Collection|Ranking[]
     */
    public function getOverallRankings(): Collection
    {
        return $this->overall_rankings;
    }

    /**
     * @return Collection|Race[]
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(Race $race): self
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
        }

        return $this;
    }

    public function removeRace(Race $race): self
    {
        if ($this->races->contains($race)) {
            $this->races->removeElement($race);
        }

        return $this;
    }

    public function getRegistrationDueDate(): ?\DateTimeInterface
    {
        return $this->registration_due_date;
    }

    public function setRegistrationDueDate(\DateTimeInterface $registration_due_date): self
    {
        $this->registration_due_date = $registration_due_date;

        return $this;
    }
}
