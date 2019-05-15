<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="view_team")
 * @ORM\Entity(readOnly=true)
 */
class ViewTeam
{

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    const GENDER_MIXED = 'X';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_athlete;

    /**
     * @ORM\Column(type="string")
     */
    private $gender;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="official_teams")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $race;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Team", inversedBy="view_team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;


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
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
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

    public function getCategoryHuman(): ?string
    {
        switch ($this->gender){
            case OfficialTeam::GENDER_MALE:
                return 'Homme';
            case OfficialTeam::GENDER_FEMALE:
                return 'Femme';
            case OfficialTeam::GENDER_MIXED:
            default:
                return 'Mixte';
        }
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

    public function setNumberOfAthlete(int $number_of_athlete): self
    {
        $this->number_of_athlete = $number_of_athlete;

        return $this;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }
}
