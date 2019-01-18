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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Athlete", mappedBy="club")
     */
    private $Athletes;

    public function __construct()
    {
        $this->Athletes = new ArrayCollection();
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
     * @return Collection|Athlete[]
     */
    public function getAthletes(): Collection
    {
        return $this->Athletes;
    }

    public function addAthlete(Athlete $athlete): self
    {
        if (!$this->Athletes->contains($athlete)) {
            $this->Athletes[] = $athlete;
            $athlete->setClub($this);
        }

        return $this;
    }

    public function removeAthlete(Athlete $athlete): self
    {
        if ($this->Athletes->contains($athlete)) {
            $this->Athletes->removeElement($athlete);
            // set the owning side to null (unless already changed)
            if ($athlete->getClub() === $this) {
                $athlete->setClub(null);
            }
        }

        return $this;
    }
}
