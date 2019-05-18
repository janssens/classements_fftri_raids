<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints as MyAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlannedTeamRepository")
 */
class PlannedTeam
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="planned_teams")
     */
    private $race;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Registration", inversedBy="planned_teams",cascade={"persist"})
     * @ORM\JoinTable(name="registration_planned_team",
     joinColumns={@ORM\JoinColumn(name="planned_team_id", referencedColumnName="id", nullable=false, onDelete="cascade")},
     inverseJoinColumns={@ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="cascade")})
     * @MyAssert\ValidRegistrationForPlanningTeam
     */
    private $registrations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Registration", inversedBy="planned_teams_requests",cascade={"persist"})
     * @ORM\JoinTable(name="request_planned_team",
    joinColumns={@ORM\JoinColumn(name="planned_team_id", referencedColumnName="id", nullable=false, onDelete="cascade")},
    inverseJoinColumns={@ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="cascade")})
     * @MyAssert\ValidRegistrationForPlanningTeam
     */
    private $requests;

    private $gender;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->requests = new ArrayCollection();
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

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->addPlannedTeam($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            $registration->removePlannedTeam($this);
        }

        return $this;
    }


    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Registration $registration): self
    {
        if (!$this->requests->contains($registration)) {
            $this->requests[] = $registration;
            $registration->addPlannedTeamRequest($this);
        }

        return $this;
    }

    public function removeRequest(Registration $registration): self
    {
        if ($this->requests->contains($registration)) {
            $this->requests->removeElement($registration);
            $registration->removePlannedTeamRequest($this);
        }

        return $this;
    }

    public function getAthletes(): Collection
    {
        $this->athletes = new ArrayCollection();
        foreach ($this->getRegistrations() as $registration){
            $this->athletes->add($registration->getAthlete());
        }
        foreach ($this->getRequests() as $registration){
            $this->athletes->add($registration->getAthlete());
        }
        return $this->athletes;
    }

    public function getCategory(): string {
        if (!$this->gender) {
            $category = Athlete::MALE;
            foreach ($this->getAthletes() as $athlete) {
                $category *= $athlete->getGender();
            }
            if ($category == Athlete::MALE) {
                $this->gender = OfficialTeam::GENDER_MALE;
            } elseif ($category < Athlete::FEMALE * $this->getRace()->getAthletesPerTeam()) {
                $this->gender = OfficialTeam::GENDER_MIXED;
            } else {
                $this->gender = OfficialTeam::GENDER_FEMALE;
            }
        }
        return $this->gender;
    }

    public function getCategoryHuman(): ?string
    {
        switch ($this->getCategory()){
            case OfficialTeam::GENDER_MALE:
                return 'Homme';
            case OfficialTeam::GENDER_FEMALE:
                return 'Femme';
            case OfficialTeam::GENDER_MIXED:
            default:
                return 'Mixte';
        }
    }

    public function getPoints(): int
    {
        $rank = 0;
        foreach ($this->getAthletes() as $athlete){
            foreach ($athlete->getRankings() as $ranking){
                if ($ranking->getChampionship() === $this->getRace()->getFinalOf() && $ranking->getCategory() === $this->getCategory()){
                    $rank += $ranking->getPoints();
                }
            }
        }
        return $rank;

    }
    public function getCaptain(): ?Athlete
    {
        if ($this->getRegistrations()->count()){
            return $this->getRegistrations()->first()->getAthlete();
        }elseif ($this->getRequests()->count()){
            return $this->getRequests()->first()->getAthlete();
        }else{
            return null;
        }
    }

    public function confirm(Registration $registration): self
    {
        if ($this->getRequests()->contains($registration)){
            $this->removeRequest($registration);
            $this->addRegistration($registration);
        }

        return $this;
    }

    public function decline(Registration $registration): self
    {
        if ($this->getRequests()->contains($registration)){
            $this->removeRequest($registration);
        }
        return $this;
    }

    public function getRegistrationsIds(): array
    {
        $ids = array();
        foreach ($this->getRegistrations() as $r){
            $ids[] = $r->getId();
        }
        foreach ($this->getRequests() as $r){
            $ids[] = $r->getId();
        }
        return array_unique($ids);
    }
}
