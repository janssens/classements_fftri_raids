<?php
// src/Validator/Constraints/ValidRegistrationForPlanningTeamValidator.php
namespace App\Validator\Constraints;

use App\Entity\PlannedTeam;
use App\Entity\Registration;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidRegistrationForPlanningTeamValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidRegistrationForPlanningTeam) {
            throw new UnexpectedTypeException($constraint, ValidRegistrationForPlanningTeam::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        /** @var Registration $registration */
        foreach ($value as $registration){
            if (!$registration->isRacing()){
                $this->context->buildViolation($constraint->not_racing_message)
                    ->setParameter('{{ firstname }}', $registration->getAthlete()->getFirstname())
                    ->addViolation();
            }
            $teams_by_race = array();
            /** @var PlannedTeam $plannedTeam */
            foreach ($registration->getPlannedTeams() as $plannedTeam){
                if (!isset($teams_by_race[$plannedTeam->getRace()->getId()])){
                    $teams_by_race[$plannedTeam->getRace()->getId()] = $plannedTeam;
                }else{
                    $this->context->buildViolation($constraint->already_message)
                        ->setParameter('{{ firstname }}', $registration->getAthlete()->getFirstname())
                        ->setParameter('{{ team_name }}', $plannedTeam->getName())
                        ->addViolation();
                }
            }
            foreach ($registration->getPlannedTeamsRequest() as $plannedTeam){
                if (!isset($teams_by_race[$plannedTeam->getRace()->getId()])){
                    $teams_by_race[$plannedTeam->getRace()->getId()] = $plannedTeam;
                }else{
                    $this->context->buildViolation($constraint->already_message)
                        ->setParameter('{{ firstname }}', $registration->getAthlete()->getFirstname())
                        ->setParameter('{{ team_name }}', $plannedTeam->getName())
                        ->addViolation();
                }
            }
        }

    }
}
