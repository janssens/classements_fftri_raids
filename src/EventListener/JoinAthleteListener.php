<?php

namespace App\EventListener;

use App\Entity\Athlete;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class JoinAthleteListener{

    /**
     * @var EntityManagerInterface
     */
    private $em;


    public function __construct(EntityManagerInterface $entity_manager)
    {
        $this->em = $entity_manager;
    }

    function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only for "User" entity
        if (!$entity instanceof User) {
            return;
        }
        /** @var User $user */
        $user = $entity;
        $athlete = $this->em->getRepository(Athlete::class)->findOneBy(array('email'=>$user->getEmail()));

        if (!$athlete){

        }else{
            $user->setAthlete($athlete);
        }
    }

}