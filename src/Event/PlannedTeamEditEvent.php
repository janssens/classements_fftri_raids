<?php

namespace App\Event;

use App\Entity\Athlete;
use App\Entity\PlannedTeam;
use App\Entity\Registration;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\Event;

class PlannedTeamEditEvent extends Event
{
    const NAME = 'planned_team.edit';

    private $planned_team;
    private $old_athlete;

    public function __construct(PlannedTeam $planned_team,ArrayCollection $old_athlete)
    {
        $this->planned_team = $planned_team;
        $this->old_athlete = $old_athlete;
    }

    /**
     * @return PlannedTeam
     */
    public function getPlannedTeam()
    {
        return $this->planned_team;
    }

    /**
     * @return ArrayCollection
     */
    public function getRemovedAthletes()
    {
        $return = new ArrayCollection();
        /** @var Athlete $a */
        foreach ($this->old_athlete as $a){
            if (!$this->getPlannedTeam()->getAthletes()->contains($a)){
                $return->add($a);
            }
        }
        return $return;
    }

    /**
     * @return ArrayCollection
     */
    public function getAddedAthletes()
    {
        $return = new ArrayCollection();
        /** @var Athlete $a */
        foreach ($this->getPlannedTeam()->getAthletes() as $a){
            if (!$this->old_athlete->contains($a)){
                $return->add($a);
            }
        }
        return $return;
    }

}
