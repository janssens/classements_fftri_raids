<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Race;
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

        $races = $em->getRepository(Race::class)->findAll();
        return $this->render('home.html.twig', [
            'races' => $races,
        ]);
    }
}