<?php

namespace App\Controller;

use App\Entity\PlannedTeam;
use App\Entity\Registration;
use App\Entity\Team;
use App\Event\PlannedTeamConfirmEvent;
use App\Event\PlannedTeamEditEvent;
use App\Event\PlannedTeamNewEvent;
use App\Form\PlannedTeamType;
use App\Form\TeamType;
use App\Helper\Vigenere;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/planned_team")
 */
class PlannedTeamController extends AbstractController
{

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="planned_team_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="planned_team_show", methods={"GET"})
     */
    public function show(PlannedTeam $team): Response
    {
        return $this->render('planned_team/show.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route("/{id}/resend/", name="planned_team_resend", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function resend(PlannedTeam $plannedTeam): Response
    {
        $session = new Session();
        $this->eventDispatcher->dispatch(PlannedTeamNewEvent::NAME, new PlannedTeamNewEvent($plannedTeam,(string)$plannedTeam->getCaptain()->getUser()));
        $session->getFlashBag()->add('success','Équipe relancée !');
        return $this->redirectToRoute('race_show',array('id'=>$plannedTeam->getRace()->getId()));
    }

    /**
     * @Route("/{id}/confirm/{code}", name="planned_team_confirm", methods={"GET"})
     */
    public function confirm(PlannedTeam $team,string $code): Response
    {
        $email = $this->get('App\Helper\Vigenere')->decode(urldecode($code));

        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $registration = $em->getRepository(Registration::class)->getOneRequestByEmailAndPlannedTeam($email,$team);
        if ($registration){
            $team->confirm($registration);
            $em->persist($team);
            $em->flush();
            $this->eventDispatcher->dispatch(PlannedTeamConfirmEvent::NAME_CONFIRM, new PlannedTeamConfirmEvent($team,$registration));
            $session->getFlashBag()->add('success','Merci d\'avoir confirmé, belle équipe !');
        }else{
            $session->getFlashBag()->add('warning','Il n\'y a plus rien à confirmer ici');
        }

        return $this->render('planned_team/show.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route("/{id}/decline/{code}", name="planned_team_decline", methods={"GET"})
     */
    public function decline(PlannedTeam $team,string $code): Response
    {
        $email = $this->get('App\Helper\Vigenere')->decode(urldecode($code));

        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $registration = $em->getRepository(Registration::class)->getOneRequestByEmailAndPlannedTeam($email,$team);
        if ($registration){
            $this->eventDispatcher->dispatch(PlannedTeamConfirmEvent::NAME_DECLINE, new PlannedTeamConfirmEvent($team,$registration));

            $team->decline($registration);
            $em->remove($team);
            $em->flush();
            $session->getFlashBag()->add('success','Merci d\'avoir répondu, l\'équipe a été dissoute');
            return $this->redirectToRoute('race_show', [
                'id' => $team->getRace()->getId(),
            ]);
        }else{
            $session->getFlashBag()->add('warning','Il n\'y a plus rien à décliner ici');
        }

        return $this->render('planned_team/show.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="planned_team_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, PlannedTeam $plannedTeam): Response
    {
        $this->denyAccessUnlessGranted('edit', $plannedTeam);

        $form = $this->createForm(PlannedTeamType::class, $plannedTeam);
        $old_athletes = new ArrayCollection();
        foreach ($plannedTeam->getAthletes() as $athlete){
            $old_athletes->add($athlete);
        };

        $form->handleRequest($request);

        $session = new Session();
        $race = $plannedTeam->getRace();

        if ($form->isSubmitted() && $form->isValid()) {
            $ids = $plannedTeam->getRegistrationsIds();
            if (count($ids)!=$race->getAthletesPerTeam()){
                $session->getFlashBag()->add('error','l\'équipe est incomplète : elle doit comporter '.$race->getAthletesPerTeam().' membres différents');
            }else{
                $em = $this->getDoctrine()->getManager();
                $em->persist($plannedTeam);
                $em->flush();

                $this->eventDispatcher->dispatch(PlannedTeamEditEvent::NAME, new PlannedTeamEditEvent($plannedTeam,$old_athletes,(string)$this->getUser()));

                $session->getFlashBag()->add('success',' l\'équipe a bien été éditée !');

                return $this->redirectToRoute('race_show', [
                    'id' => $plannedTeam->getRace()->getId(),
                ]);
            }
        }

        return $this->render('planned_team/edit.html.twig', [
            'team' => $plannedTeam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="planned_team_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, PlannedTeam $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $race = $team->getRace();
            $entityManager = $this->getDoctrine()->getManager();

            $this->eventDispatcher->dispatch(PlannedTeamEditEvent::DELETE_NAME, new PlannedTeamEditEvent($team,new ArrayCollection(),(string)$this->getUser()));

            $entityManager->remove($team);
            $entityManager->flush();

        }

        return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
    }
}
