<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 */
class Club
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
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="club")
     */
    private $registrations;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ranking", mappedBy="club")
     * @ORM\OrderBy({"points" = "DESC","athlete" = "DESC"})
     */
    private $rankings;

    private $_athletes;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
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
            $registration->setClub($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            // set the owning side to null (unless already changed)
            if ($registration->getLigue() === $this) {
                $registration->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAthletes(): ArrayCollection
    {
        if (!$this->_athletes){
            $added = array();
            $this->_athletes = new ArrayCollection();
            foreach ($this->registrations as $registration){
                if (!in_array($registration->getAthlete()->getId(),$added)) {
                    $this->_athletes->add($registration->getAthlete());
                    $added[] = $registration->getAthlete()->getId();
                }
            }
        }
        return $this->_athletes;
    }

    public function getUptodateAthletes(): ArrayCollection
    {
        /** @athlete Athlete */
        return $this->getAthletes()->filter(function ($athlete) {
            return $athlete->getLastRegistration()->isValidForDate(new \DateTime('now'));
        });
    }

    public function getOldAthletes(): ArrayCollection
    {
        /** @athlete Athlete */
        return $this->getAthletes()->filter(function ($athlete) {
            return !$athlete->getLastRegistration()->isValidForDate(new \DateTime('now'));
        });
    }

    /**
     * @return Collection|Ranking[]
     */
    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
