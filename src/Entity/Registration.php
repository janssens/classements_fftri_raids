<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint as UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegistrationRepository")
 * @ORM\Table(name="registration",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="registration_unique",
 *            columns={"number", "date"})
 *    }
 * )
 */
class Registration
{
    const TYPE_A = 1;
    const TYPE_B = 2;
    const TYPE_C = 3;
    const TYPE_D = 4;
    const TYPE_H = 7;
    const TYPE_NULL = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $number;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ask_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="registrations",cascade={"persist"})
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ligue", inversedBy="registrations",cascade={"persist"})
     */
    private $ligue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Athlete", inversedBy="registrations",cascade={"persist"})
     */
    private $athlete;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", inversedBy="registrations",cascade={"persist"})
     */
    private $teams;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_long;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return $this->getNumber();
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAskDate(): ?\DateTimeInterface
    {
        return $this->ask_date;
    }

    public function setAskDate(\DateTimeInterface $ask_date): self
    {
        $this->ask_date = $ask_date;

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

    public function getEndDate(): ?\DateTimeInterface
    {
//        if ($this->is_long)
//            return date_create_from_format('d/m/Y H:i:s','31/12/'.(intval($this->getDate()->format('Y'))+1).' 23:59:59');
        return date_create_from_format('d/m/Y H:i:s','31/12/'.$this->getDate()->format('Y').' 23:59:59');
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getLigue(): ?Ligue
    {
        return $this->ligue;
    }

    public function setLigue(?Ligue $ligue): self
    {
        $this->ligue = $ligue;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAthlete(): ?Athlete
    {
        return $this->athlete;
    }

    public function setAthlete(?Athlete $athlete): self
    {
        $this->athlete = $athlete;

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
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
        }

        return $this;
    }

    public function getIsLong(): ?bool
    {
        return $this->is_long;
    }

    public function setIsLong(bool $is_long): self
    {
        $this->is_long = $is_long;

        return $this;
    }
}
