<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RaceRepository")
 */
class Race
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $lon;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="race", orphanRemoval=true, cascade={"remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OfficialTeam", mappedBy="race")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $official_teams;

    /**
     * @ORM\Column(type="integer")
     */
    private $athletes_per_team;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Championship", mappedBy="races")
     */
    private $championships;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->official_teams = new ArrayCollection();
        $this->championships = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getLat()
    {
        return $this->lat;
    }

    public function setLat($lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function setLon($lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setRace($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getRace() === $this) {
                $team->setRace(null);
            }
        }

        return $this;
    }

    public function getAthletesPerTeam(): ?int
    {
        return $this->athletes_per_team;
    }

    public function setAthletesPerTeam(int $athletes_per_team): self
    {
        $this->athletes_per_team = $athletes_per_team;

        return $this;
    }

    /**
     * @return Collection|OfficialTeam[]
     */
    public function getOfficialTeams(): Collection
    {
        return $this->official_teams;
    }

    /**
     * @return Collection|Championship[]
     */
    public function getChampionships(): Collection
    {
        return $this->championships;
    }

    public function addChampionship(Championship $championship): self
    {
        if (!$this->championships->contains($championship)) {
            $this->championships[] = $championship;
            $championship->addRace($this);
        }

        return $this;
    }

    public function removeChampionship(Championship $championship): self
    {
        if ($this->championships->contains($championship)) {
            $this->championships->removeElement($championship);
            $championship->removeRace($this);
        }

        return $this;
    }

}
