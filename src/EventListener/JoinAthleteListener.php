<?php

namespace App\EventListener;

use App\Entity\Athlete;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class JoinAthleteListener{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TokenStorage
     */
    private $token_storage;
    /**
     * @var Router
     */
    private $router;


    public function __construct(EntityManagerInterface $entity_manager, TokenStorage $token_storage,Router $router)
    {
        $this->em = $entity_manager;
        $this->token_storage = $token_storage;
        $this->router = $router;
    }

    function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only for users created trow "User" entity
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