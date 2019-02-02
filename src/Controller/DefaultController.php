<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Championship;
use App\Entity\Race;
use App\Entity\Ranking;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
    * @Route("/",name="home")
    */
    public function home()
    {
        $em = $this->container->get('doctrine')->getManager();

        $rankings = $em->getRepository(Championship::class)->find(1)->getRankings();
        return $this->render('home.html.twig', [
            'rankings' => $rankings,
        ]);
    }
}