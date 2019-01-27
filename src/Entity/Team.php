<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
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
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="teams")
     */
    private $race;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Registration", mappedBy="teams")
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outsider", mappedBy="team",orphanRemoval=true, cascade={"remove"})
     */
    private $outsiders;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->outsiders = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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
            $registration->addTeam($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            $registration->removeTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection|Outsider[]
     */
    public function getOutsiders(): Collection
    {
        return $this->outsiders;
    }

    public function addOutsider(Outsider $outsider): self
    {
        if (!$this->outsiders->contains($outsider)) {
            $this->outsiders[] = $outsider;
            $outsider->setTeam($this);
        }

        return $this;
    }

    public function removeOutsider(Outsider $outsider): self
    {
        if ($this->outsiders->contains($outsider)) {
            $this->outsiders->removeElement($outsider);
            // set the owning side to null (unless already changed)
            if ($outsider->getTeam() === $this) {
                $outsider->setTeam(null);
            }
        }

        return $this;
    }
}
