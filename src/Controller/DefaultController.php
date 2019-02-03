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
        return $this->redirectToRoute('championship_index');
    }
}