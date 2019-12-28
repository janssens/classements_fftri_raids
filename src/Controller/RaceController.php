<?php

namespace App\Controller;

use App\Entity\PlannedTeam;
use App\Entity\Race;
use App\Entity\Registration;
use App\Event\PlannedTeamNewEvent;
use App\Form\PlannedTeamType;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/race")
 */
class RaceController extends Controller
{

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="race_index", methods="GET")
     */
    public function index(RaceRepository $raceRepository): Response
    {
        return $this->render('race/index.html.twig', ['races' => $raceRepository->findAll()]);
    }

    /**
     * @Route("/new", name="race_new", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            return $this->redirectToRoute('race_index');
        }

        return $this->render('race/new.html.twig', [
            'race' => $race,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="race_show", methods="GET")
     */
    public function show(Race $race): Response
    {
        $sortedPlannedTeams = array();
        foreach ($race->getPlannedTeams() as $team){
            if (!isset($sortedPlannedTeams[$team->getCategoryHuman()])){
                $sortedPlannedTeams[$team->getCategoryHuman()] = array();
            }
            $sortedPlannedTeams[$team->getCategoryHuman()][] = array('points'=>$team->getPoints(),'team'=>$team);
        }
        foreach ($sortedPlannedTeams as $key => $array){
            uasort($array, function ($a, $b) {
                if ($a['points'] == $b['points']) {
                    return 0;
                }
                return ($a['points'] > $b['points']) ? -1 : 1;
            });
            $sortedPlannedTeams[$key] = $array;
        }

        return $this->render('race/show.html.twig', ['race' => $race,'sortedPlannedTeams' => $sortedPlannedTeams]);
    }

/**
     * @Route("/{id}/edit", name="race_edit", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Race $race): Response
    {
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('race_edit', ['id' => $race->getId()]);
        }

        return $this->render('race/edit.html.twig', [
            'race' => $race,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="race_delete", methods="DELETE")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, Race $race): Response
    {
        if ($this->isCsrfTokenValid('delete'.$race->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($race);
            $em->flush();
        }

        return $this->redirectToRoute('race_index');
    }

    /**
     * @Route("/{id}/planned_team/new", name="planned_team_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function newPlannedTeam(Request $request, Race $race): Response
    {
        $session = new Session();

        if (!$this->isGranted('ROLE_ADMIN')&&!$this->getUser()->getAthlete()){
            $session->getFlashBag()->add('warning','l\'utilisateur de l\'application doit être associé à un athlete pour créer une équipe');
            return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
        }

        $date = $race->getFinalOf()->getFinalRegistrationDueDate();
        if ($date < new \DateTime()){
            $session->getFlashBag()->add('error','Pre-inscriptions closes');
            return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
        }

        $plannedTeam = new PlannedTeam();
        $plannedTeam->setRace($race);

        if (!$this->isGranted('ROLE_ADMIN')){
            $exist = false;
            foreach ($this->getUser()->getAthlete()->getPlannedTeam() as $pt){
                if ($pt->getRace() === $race){
                    $exist = $pt->getId();
                    break;
                }
            }
            if ($exist){
                $session->getFlashBag()->add('warning','Tu as déjà une équipe pour cette course');
                return $this->redirectToRoute('planned_team_show',array('id'=>$exist));
            }
            $plannedTeam->addRegistration($this->getUser()->getAthlete()->getRegistrations()->first());
        }


        for ($i = $plannedTeam->getRegistrations()->count(); $i < $race->getAthletesPerTeam();$i++){
            $plannedTeam->addRequest(new Registration());
        }
        $form = $this->createForm(PlannedTeamType::class, $plannedTeam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ids = $plannedTeam->getRegistrationsIds();
            if (count($ids)!=$race->getAthletesPerTeam()){
                $session->getFlashBag()->add('error','l\'équipe est incomplète : elle doit comporter '.$race->getAthletesPerTeam().' membres différents');
            }else{
                $em = $this->getDoctrine()->getManager();

                $em->persist($plannedTeam);
                $em->flush();

                $this->eventDispatcher->dispatch(PlannedTeamNewEvent::NAME, new PlannedTeamNewEvent($plannedTeam,(string)$this->getUser()));

                $session->getFlashBag()->add('success',' l\'équipe a bien été créée !');

                return $this->redirectToRoute('race_show', [
                    'id' => $plannedTeam->getRace()->getId(),
                ]);
            }
        }

        return $this->render('planned_team/new.html.twig', [
            'plannedTeam' => $plannedTeam,
            'form' => $form->createView(),
        ]);
    }
}
