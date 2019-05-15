<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AthleteRepository")
 */
class Athlete
{
    const FEMALE = 2;
    const MALE = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dob;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="athlete",cascade={"persist"})
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ranking", mappedBy="athlete")
     */
    private $rankings;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Racer", mappedBy="parent")
     */
    private $racer;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="athlete")
     */
    private $user;

    private $planned_teams;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->rankins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return ucfirst(strtolower($this->firstname));
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return strtoupper($this->lastname);
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setAthlete($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            // set the owning side to null (unless already changed)
            if ($registration->getAthlete() === $this) {
                $registration->setAthlete(null);
            }
        }

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
     * @return Racer
     */
    public function getRacer(): Racer
    {
        return $this->racer;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlannedTeam(): ?Collection
    {
        if (!$this->planned_teams) {
            $this->planned_teams = new ArrayCollection();
            foreach ($this->getRegistrations() as $registration) {
                foreach ($registration->getPlannedTeams() as $plannedTeam) {
                    $this->planned_teams->add($plannedTeam);
                }
            }
        }
        return $this->planned_teams;
    }

    public function getPlannedTeamByRace(Race $race): ?Collection
    {
        if (!$this->planned_teams)
            return null;
        return $this->planned_teams
            ->filter(function(PlannedTeam $plannedTeam) use ($race){
                return $plannedTeam->getRace() === $race;
            });
    }
}
