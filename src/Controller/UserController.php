<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\User;
use App\Event\PlannedTeamConfirmEvent;
use App\Event\UserEvent;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/user")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class UserController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/resend_token", name="user_resend_token", methods={"GET"})
     */
    public function resendToken(User $user): Response
    {
        $session = new Session();
        $this->eventDispatcher->dispatch(UserEvent::NAME_RESEND_TOKEN,new UserEvent($user));
        $session->getFlashBag()->add('success','utilisateur relancé !');
        return $this->redirectToRoute('user_index');
    }

}
