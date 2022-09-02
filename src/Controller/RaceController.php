<?php

namespace App\Controller;

use App\Command\ImportRaceRanking;
use App\Entity\Athlete;
use App\Entity\Outsider;
use App\Entity\PlannedTeam;
use App\Entity\Race;
use App\Entity\Registration;
use App\Entity\Team;
use App\Event\PlannedTeamNewEvent;
use App\Form\ImportType;
use App\Form\PlannedTeamType;
use App\Form\RaceType;
use App\Helper\Csv;
use App\Repository\RaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Vich\UploaderBundle\Storage\AbstractStorage;
use Vich\UploaderBundle\Storage\FileSystemStorage;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use function PHPUnit\Framework\throwException;

/**
 * @Route("/race")
 */
class RaceController extends AbstractController
{

    private $eventDispatcher;
    private $fileSystemStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher,FileSystemStorage $fileSystemStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->fileSystemStorage = $fileSystemStorage;
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
     * @Route("/{id}/import_ranking", name="race_import_ranking", methods="GET|POST")
     */
    public function importRanking(Race $race,Request $request): Response
    {
        $session = new Session();

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if (!$race->getRankingFilename()) {
            $session->getFlashBag()->add('error', 'Merci de joindre le classement à la course');
            return $this->redirectToRoute('race_edit',['id'=>$race->getId()]);
        }

        $csvHelper = new Csv();
        $neededFields = array(
            'position' => array('label' => 'Classement scratch','index'=>0),
            'team_name' => array('label' => 'Nom d\'équipe','index'=>1),
        );
        for ($i = 0; $i < $race->getAthletesPerTeam() ; $i++){
            $neededFields['firstname_'.$i] = array('label' => 'Prénom athlète #'.($i+1),'index'=>$i*4+2,"required" => false,"default" =>'Équipier');
            $neededFields['lastname_'.$i] = array('label' => 'Nom athlète #'.($i+1),'index'=>$i*4+3,"required" => false,"default" =>'#'.($i+1));
            $neededFields['number_'.$i] = array('label' => 'Licence athlète #'.($i+1),'index'=>$i*4+4);
            $neededFields['gender_'.$i] = array('label' => 'Sexe athlète #'.($i+1),'index'=>$i*4+5);
        }
        $file = $this->fileSystemStorage->resolvePath($race,'rankingFile');

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $map = explode(',',$form->get("map")->getData());

            if (count($map)!=2+4*$race->getAthletesPerTeam()){
                throw new \Exception('colonnes manquante dans la map');
            }

            foreach ($race->getTeams() as $team){
                $race->removeTeam($team);
                $em->remove($team);
            }
            $em->persist($race);

            $delimiter = $form->get("delimiter")->getData();

            $csvHelper->setDelimiter($delimiter);
            $csvHelper->setNeededFields($neededFields);
            $csvHelper->setMap($map);

            $row = 0;
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    $row++;
                    if ($row > 1) { //skip first line

                        $team_name = $csvHelper->getField('team_name', $data);
                        if ($team_name and strlen($team_name)>1){

                            $team = new Team();
                            $team->setName($team_name);
                            $team->setPosition(intval($csvHelper->getField('position', $data)));

                            for ($i = 0; $i < $race->getAthletesPerTeam(); $i++) {
                                $number = substr($csvHelper->getField('number_' . $i, $data),0,6);
                                if (strlen($number) == 6) {
                                    //Search athlete with licence :
                                    $registration = $em->getRepository(Registration::class)->findOneByLicenceAndRace($number,$race);
                                    /** @var Registration $registration */
                                    if ($registration) {
                                        $team->addRegistration($registration);
                                        $registration->addTeam($team);
                                    } else {
                                        $outsider = new Outsider();
                                        $outsider->setFirstname($csvHelper->getField('firstname_' . $i, $data));
                                        $outsider->setLastname($csvHelper->getField('lastname_' . $i, $data));
                                        $outsider->setGender((($csvHelper->getField('gender_' . $i, $data) == 'm')||($csvHelper->getField('gender_' . $i, $data) == 'h')) ? Athlete::MALE : Athlete::FEMALE);
                                        $outsider->setNumber($csvHelper->getField('number_' . $i, $data));
                                        $outsider->setTeam($team);
                                        $em->persist($outsider);
                                        $team->addOutsider($outsider);
                                    }
                                } else {
                                    $outsider = new Outsider();
                                    $outsider->setFirstname($csvHelper->getField('firstname_' . $i, $data));
                                    $outsider->setLastname($csvHelper->getField('lastname_' . $i, $data));
                                    $outsider->setGender((($csvHelper->getField('gender_' . $i, $data) == 'm')||($csvHelper->getField('gender_' . $i, $data) == 'h')) ? Athlete::MALE : Athlete::FEMALE);
                                    $outsider->setNumber(null);
                                    $outsider->setTeam($team);
                                    $em->persist($outsider);
                                    $team->addOutsider($outsider);
                                }
                            }
                            $team->setRace($race);
                            $em->persist($team);
                        }
                    }
                }
                $em->flush();
                fclose($handle);
                $session->getFlashBag()->add('success', 'Le classement a été traité avec succés');
                return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
            }else{
                $session->getFlashBag()->add('error', 'impossible de lire le fichier du classement');
                return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
            }

        }else{
            $delimiter = $csvHelper->guessDelimiter($file);
            if (!$delimiter){
                throw new \Exception('could not determine csv delimiter');
            }
            $all = [];
            if (($handle = fopen($file, "r")) !== FALSE) {
                $count = 0;
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE or $count < 5) {
                    $all[$count++] = $data;
                }
            }
            $csvHelper->setNeededFields($neededFields);

            return $this->render('race/import.html.twig', [
                'race'=>$race,
                'lines'=>$all,
                'form'=>$form->createView(),
                'delimiter'=>$delimiter,
                'map'=>$csvHelper->getMap()
            ]);
        }
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

            return $this->redirectToRoute('race_show', ['id' => $race->getId()]);
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
