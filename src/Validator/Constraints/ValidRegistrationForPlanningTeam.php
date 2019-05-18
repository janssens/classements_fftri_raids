<?php
// src/Validator/Constraints/ValidRegistrationForPlanningTeam.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/
class ValidRegistrationForPlanningTeam extends Constraint
{
    public $message = 'Cette équipe n\'est pas valide.';
    public $not_racing_message = '{{ firstname }} n\'a pas une licence compétition !';
    public $already_message = '{{ firstname }} apparait déjà dans une autre équipe  pour cette course ! (Equipe : {{ team_name }})';

}