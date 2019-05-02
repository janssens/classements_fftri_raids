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
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
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
        if ($this->getUser()->isGranted('ROLE_ADMIN')){
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
}