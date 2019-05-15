<?php

namespace App\Event;

use App\Entity\PlannedTeam;
use App\Entity\Registration;
use Symfony\Component\EventDispatcher\Event;

class PlannedTeamConfirmEvent extends Event
{
    const NAME_CONFIRM = 'planned_team.confirm';
    const NAME_DECLINE = 'planned_team.decline';

    private $planned_team;
    private $registration;

    public function __construct(PlannedTeam $team,Registration $registration)
    {
        $this->planned_team = $team;
        $this->registration = $registration;
    }

    /**
     * @return PlannedTeam
     */
    public function getPlannedTeam()
    {
        return $this->planned_team;
    }

    /**
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

}
