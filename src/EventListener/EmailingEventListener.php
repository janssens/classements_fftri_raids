<?php

namespace App\EventListener;

use App\Entity\Athlete;
use App\Entity\PlannedTeam;
use App\Entity\Registration;
use App\Event\PlannedTeamConfirmEvent;
use App\Event\PlannedTeamEditEvent;
use App\Event\PlannedTeamNewEvent;
use App\Helper\Vigenere;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Swift_Mailer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailingEventListener
{
    protected $mailer;
    protected $container;
    private $fromEmail;

    public function __construct(Swift_Mailer $mailer, Container $container,$fromEmail)
    {
        $this->mailer = $mailer;
        $this->container = $container;
        $this->fromEmail = $fromEmail;
    }

    /**
     * @param PlannedTeamConfirmEvent $event
     * @throws \Exception
     */
    public function onPlannedTeamConfirm(PlannedTeamConfirmEvent $event) {
        $pt = $event->getPlannedTeam();
        $reg = $event->getRegistration();

        $needInfo = (new \Swift_Message($reg->getAthlete()->getFirstname().' a accepté ton invitation pour le '.$pt->getRace()->getFinalOf()->getName()))
            ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
            ->setTo($pt->getCaptain()->getEmail())
            ->setReplyTo($reg->getAthlete()->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/confirmDone.html.twig',
                    array(
                        'planning_team' => $pt,
                        'athlete' => $reg->getAthlete(),
                    )
                ),
                'text/html'
            );
        $this->mailer->send($needInfo);

    }

    /**
     * @param PlannedTeamConfirmEvent $event
     * @throws \Exception
     */
    public function onPlannedTeamDecline(PlannedTeamConfirmEvent $event) {
        $pt = $event->getPlannedTeam();
        $reg = $event->getRegistration();

        $needInfo = (new \Swift_Message($reg->getAthlete()->getFirstname().' a décliné ton invitation pour le '.$pt->getRace()->getFinalOf()->getName()))
            ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
            ->setTo($pt->getCaptain()->getEmail())
            ->setReplyTo($reg->getAthlete()->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/decline.html.twig',
                    array(
                        'planning_team' => $pt,
                        'athlete' => $reg->getAthlete(),
                    )
                ),
                'text/html'
            );
        $this->mailer->send($needInfo);

    }

    /**
     * @param PlannedTeamNewEvent $event
     * @throws \Exception
     */
    public function onPlannedTeamNew(PlannedTeamNewEvent $event)
    {
        $pt = $event->getPlannedTeam();
        $vigenereHelper = new Vigenere($this->container);

        foreach ($pt->getRequests() as $registration){
            $athlete = $registration->getAthlete();
            $email = $athlete->getEmail();
            $accepte_url = $this->container->get('router')->generate('planned_team_confirm', array('id' => $pt->getId(),'code' => urlencode($vigenereHelper->encode($email))),UrlGeneratorInterface::ABSOLUTE_URL);
            $decline_url = $this->container->get('router')->generate('planned_team_decline', array('id' => $pt->getId(),'code' => urlencode($vigenereHelper->encode($email))),UrlGeneratorInterface::ABSOLUTE_URL);

            $needInfo = (new \Swift_Message( $pt->getCaptain()->getFirstname().' t\'invite dans son équipe pour le'.$pt->getRace()->getFinalOf()->getName()))
            ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'emails/confirmPlanningTeam.html.twig',
                    array(
                        'planning_team' => $pt,
                        'athlete' => $athlete,
                        'accepte_url' => $accepte_url,
                        'decline_url' => $decline_url
                    )
                ),
                'text/html'
            );
            $this->mailer->send($needInfo);
        }

    }

    public function onPlannedTeamEdit(PlannedTeamEditEvent $event){
        $pt = $event->getPlannedTeam();
        $vigenereHelper = new Vigenere($this->container);
        /* @var $athlete Athlete */
        foreach ($event->getRemovedAthletes() as $athlete){
            $email = $athlete->getEmail();
            $needInfo = (new \Swift_Message( $pt->getCaptain()->getFirstname().' a changé son équipe pour le '.$pt->getRace()->getFinalOf()->getName()))
                ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'emails/removed.html.twig',
                        array(
                            'planning_team' => $pt,
                            'athlete' => $athlete,
                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($needInfo);
        }
        /* @var $athlete Athlete */
        foreach ($event->getAddedAthletes() as $athlete){
            $email = $athlete->getEmail();
            $accepte_url = $this->container->get('router')->generate('planned_team_confirm', array('id' => $pt->getId(),'code' => urlencode($vigenereHelper->encode($email))),UrlGeneratorInterface::ABSOLUTE_URL);
            $decline_url = $this->container->get('router')->generate('planned_team_decline', array('id' => $pt->getId(),'code' => urlencode($vigenereHelper->encode($email))),UrlGeneratorInterface::ABSOLUTE_URL);

            $needInfo = (new \Swift_Message( $pt->getCaptain()->getFirstname().' t\'invite dans son équipe pour le'.$pt->getRace()->getFinalOf()->getName()))
                ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'emails/confirmPlanningTeam.html.twig',
                        array(
                            'planning_team' => $pt,
                            'athlete' => $athlete,
                            'accepte_url' => $accepte_url,
                            'decline_url' => $decline_url
                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($needInfo);
        }
    }

    public function onPlannedTeamDelete(PlannedTeamEditEvent $event){
        $pt = $event->getPlannedTeam();
        /* @var $athlete Athlete */
        foreach ($event->getPlannedTeam()->getAthletes() as $athlete){
            $email = $athlete->getEmail();
            $needInfo = (new \Swift_Message( $pt->getCaptain()->getFirstname().' a supprimé l\'équipe pour le '.$pt->getRace()->getFinalOf()->getName()))
                ->setFrom($this->fromEmail['address'], $this->fromEmail['sender_name'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'emails/delete.html.twig',
                        array(
                            'planning_team' => $pt,
                            'athlete' => $athlete,
                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($needInfo);
        }
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     * @throws \Exception
     */
    protected function renderView($view, array $parameters = array())
    {
        if ($this->container->has('templating')) {
            return $this->container->get('templating')->render($view, $parameters);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available.');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }

}
