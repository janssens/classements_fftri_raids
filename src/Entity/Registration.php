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
    const TYPE_A = 1; //A - Lic. club - Compétition - Jeune
    const TYPE_B = 2; //B - Lic. club - Compétition - S. & V.
    const TYPE_C = 3; //C - Lic. club - Loisir - Jeune
    const TYPE_D = 4; //D - Licence club - Loisir - S. & V.
    const TYPE_E = 5; //E - Paratriathlon - Lic. club - Comp�tition - Jeune
    const TYPE_F = 6; //F - Paratriathlon - Lic. club - Comp�tition  - S. & V.
    const TYPE_G = 7; //G - Lic. club - Dirigeant
    const TYPE_H = 8; //H - Lic. Individuelle - Comp�tition - S. & V.
    const TYPE_I = 9; //I - Paratriathlon  - Lic. individuelle - Comp�tition  - S. & V.
    const TYPE_J = 10; //J - Lic. individuelle - Dirigeant
    const TYPE_K = 11; //K - Paratriathlon- Lic. club - Loisir - Jeune
    const TYPE_L = 12; //L - Paratriathlon- Lic. Club - Loisir - S. & V.
    const TYPE_M = 13; //M - Lic. club - Action
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
     * @ORM\ManyToMany(targetEntity="App\Entity\PlannedTeam", mappedBy="registrations",cascade={"all"})
     */
    private $planned_teams;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PlannedTeam", mappedBy="requests",cascade={"all"})
     */
    private $planned_teams_requests;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_long;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end_date;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->planned_teams = new ArrayCollection();
        $this->planned_teams_requests = new ArrayCollection();
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
        return $this->end_date;
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

    public function isRacing(){
        return self::typeIsRacing($this->getType());
    }

    public function isLoisir(){
        return self::typeIsLoisir($this->getType());
    }

    public function isDir(){
        return self::typeIsDir($this->getType());
    }

    public static function typeIsRacing($type){
        return in_array($type,array(self::TYPE_A,self::TYPE_B,self::TYPE_E,self::TYPE_F,self::TYPE_H,self::TYPE_I));
    }

    public static function typeIsLoisir($type){
        return in_array($type,array(self::TYPE_C,self::TYPE_D,self::TYPE_K,self::TYPE_L));
    }

    public static function typeIsDir($type){
        return in_array($type,array(self::TYPE_G,self::TYPE_J));
    }

    public static function typesAreFromSameCategory($typeA,$typeB){
        return ((self::typeIsRacing($typeA)&&self::typeIsRacing($typeB)||(self::typeIsLoisir($typeA)&&self::typeIsLoisir($typeB))||self::typeIsDir($typeA)&&self::typeIsDir($typeB)));
    }

    /**
     * @param $date \DateTime
     * @return bool
     */
    public function isValidForDate($date){
        return $date > $this->getStartDate() && $date < $this->getEndDate();
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

    /**
     * @return Collection|PlannedTeam[]
     */
    public function getPlannedTeams(): Collection
    {
        return $this->planned_teams;
    }


    public function addPlannedTeam(PlannedTeam $plannedTeam): self
    {
        if (!$this->planned_teams->contains($plannedTeam)) {
            $this->planned_teams[] = $plannedTeam;
            $plannedTeam->addRegistration($this);
        }

        return $this;
    }

    public function removePlannedTeam(PlannedTeam $plannedTeam): self
    {
        if ($this->planned_teams->contains($plannedTeam)) {
            $this->planned_teams->removeElement($plannedTeam);
            $plannedTeam->removeRegistration($this);
        }

        return $this;
    }

    /**
     * @return Collection|PlannedTeam[]
     */
    public function getPlannedTeamsRequest(): Collection
    {
        return $this->planned_teams_requests;
    }

    public function addPlannedTeamRequest(PlannedTeam $plannedTeam): self
    {
        if (!$this->planned_teams_requests->contains($plannedTeam)) {
            $this->planned_teams_requests[] = $plannedTeam;
            $plannedTeam->addRequest($this);
        }

        return $this;
    }

    public function removePlannedTeamRequest(PlannedTeam $plannedTeam): self
    {
        if ($this->planned_teams_requests->contains($plannedTeam)) {
            $this->planned_teams_requests->removeElement($plannedTeam);
            $plannedTeam->removeRequest($this);
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }
}
