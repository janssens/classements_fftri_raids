<?php

namespace App\Event;

use App\Entity\PlannedTeam;
use Symfony\Component\EventDispatcher\Event;

class PlannedTeamNewEvent extends Event
{
    const NAME = 'planned_team.new';

    private $planned_team;

    public function __construct(PlannedTeam $team)
    {
        $this->planned_team = $team;
    }

    /**
     * @return PlannedTeam
     */
    public function getPlannedTeam()
    {
        return $this->planned_team;
    }

}
