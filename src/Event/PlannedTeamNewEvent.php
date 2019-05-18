<?php

namespace App\Event;

use App\Entity\PlannedTeam;
use Symfony\Component\EventDispatcher\Event;

class PlannedTeamNewEvent extends Event
{
    const NAME = 'planned_team.new';

    private $planned_team;
    private $author;

    public function __construct(PlannedTeam $team,string $author)
    {
        $this->planned_team = $team;
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * @return PlannedTeam
     */
    public function getPlannedTeam()
    {
        return $this->planned_team;
    }

}
