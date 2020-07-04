<?php

namespace App\Event;

use App\Entity\PlannedTeam;
use App\Entity\Registration;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    const NAME = 'user';
    const NAME_RESEND_TOKEN = 'user.resend_token';

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
