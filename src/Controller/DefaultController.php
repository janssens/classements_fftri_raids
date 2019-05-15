<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Athlete;
use App\Entity\Championship;
use App\Entity\Club;
use App\Entity\Outsider;
use App\Entity\Race;
use App\Entity\Racer;
use App\Entity\Ranking;
use App\Entity\Registration;
use App\Entity\Season;
use App\EventListener\SetFirstPasswordListener;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class DefaultController extends Controller
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
    * @Route("/",name="home")
    */
    public function home()
    {
        return $this->redirectToRoute('championship_index');
    }

    /**
     * @Route("/search",name="search")
     */
    public function search(Request $request)
    {
        $s = $request->get('s');
        $em = $this->getDoctrine()->getManager();
        $races = $em->getRepository(Race::class)->findByString($s);
        $racers = $em->getRepository(Racer::class)->findByString($s);
        $clubs = $em->getRepository(Club::class)->findByString($s);
        $athletes = array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $athletes = $em->getRepository(Athlete::class)->findByString($s);
        }

        return $this->render('search.html.twig', [
            's' => $s,
            'races' => $races,
            'racers' => $racers,
            'clubs' => $clubs,
            'athletes' => $athletes
        ]);
    }

    /**
     * @Route("/registrations/list", name="registration_list", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_USER")
     */
    public function listAction(Request $request){

        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();

            $string = $request->get('query');

            $rsm = new ResultSetMappingBuilder($em);
            $rsm->addRootEntityFromClassMetadata('App:Registration', 'b');

/*            $query = $em->createNativeQuery('SELECT r.* FROM registration AS r '.
                'join athlete AS a ON a.id = r.athlete_id '.
                'join registration_team as rt ON rt.registration_id = r.id '.
                'WHERE DATE(r.end_date) > DATE(NOW()) AND LOWER(CONCAT(a.lastname,a.firstname)) LIKE :key', $rsm);*/

            $query = $em->createNativeQuery('SELECT r.* FROM registration AS r '.
                'join athlete AS a ON a.id = r.athlete_id '.
                'join club AS c ON c.id = r.club_id '.
                'left join registration AS copain ON copain.club_id = r.club_id '.
                'join registration_team as rt ON rt.registration_id = copain.id '.
                'WHERE DATE(r.end_date) > DATE(NOW()) AND LOWER(CONCAT(a.lastname,a.firstname)) LIKE :key GROUP BY email;', $rsm);

            $registrations = $query->setParameter('key', '%' . $string . '%')
                ->getResult();

            $returnArray = array();
            foreach ($registrations as $registration){
                $returnArray[] = array('value' => $registration->getAthlete()->getFullName(),'data'=>$registration->getId());
            }
            return new JsonResponse(array('suggestions'=>$returnArray));
        }
        return new Response("Ajax only",400);
    }

    /**
     * change_password
     *
     * @Route("/change_password", name="user_change_password", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('password',PasswordType::class,array('label'=>'Un mot de passe','trim'=>true));
        $formBuilder->add('password_repeat',PasswordType::class,array('label'=>'Le même une deuxième fois','trim'=>true));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getData()['password'] === $form->getData()['password_repeat']){
                $this->getUser()->setPlainPassword($form->getData()['password']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($this->getUser());
                $em->flush();

                $event = new UserEvent($this->getUser(), $request);
                $this->eventDispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, $event);

                $session = new Session();
                $session->getFlashBag()->add('success', 'Mot de passe enregistré, merci !');

                return $this->redirectToRoute('home');
            }else{
                $session = new Session();
                $session->getFlashBag()->add('error','Attention : tes deux mots de passe ne sont pas identique !');
            }

        }

        return $this->render('user/change_password.html.twig',array('form'=>$form->createView()));
    }
}