<?php
// src/Security/PlannedTeamVoter.php
namespace App\Security;

use App\Entity\PlannedTeam;
use App\Entity\Registration;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PlannedTeamVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT,self::DELETE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof PlannedTeam) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // you know $subject is a PlannedTeam object, thanks to supports
        /** @var PlannedTeam $post */
        $team = $subject;

        switch ($attribute) {
            case self::VIEW:
                return true;
            case self::DELETE:
            case self::EDIT:
                return $this->canEdit($team, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(PlannedTeam $team, User $user)
    {
        $confirmed_athlete = new ArrayCollection();
        /** @var Registration $registration */
        foreach ($team->getRegistrations() as $registration){
            $confirmed_athlete->add($registration->getAthlete());
        }
        return $user->getAthlete() && $confirmed_athlete->contains($user->getAthlete());
    }
}
