<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

class SetFirstPasswordListener{

    const  ROLE_PASSWORD_TO_SET  = 'ROLE_PASSWORD_TO_SET';

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UsageTrackingTokenStorage
     */
    private $token_storage;
    /**
     * @var Router
     */
    private $router;


    public function __construct(EntityManagerInterface $entity_manager, UsageTrackingTokenStorage $token_storage,Router $router)
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

        if (!$user->getId()){
            $user->addRole(self::ROLE_PASSWORD_TO_SET);
            $user->setPassword(\bin2hex(\random_bytes(20)));
        }
    }

    function onPasswordChanged(UserEvent $event)
    {
        $user = $event->getUser();
        $user->removeRole(self::ROLE_PASSWORD_TO_SET);
        $this->em->persist($user);
        $this->em->flush();
    }

    function forcePasswordChange(RequestEvent $event){

        $token = $this->token_storage->getToken();
        if ($token){
            $currentUser = $token->getUser();
            if($currentUser instanceof User){
                if($currentUser->hasRole(self::ROLE_PASSWORD_TO_SET)){
                    $route = $event->getRequest()->get('_route');
                    if ($route && $route != 'user_change_password'){
                        $changePassword = $this->router->generate('user_change_password');
                        $event->setResponse(new RedirectResponse($changePassword));
                    }
                }
            }
        }

    }

}