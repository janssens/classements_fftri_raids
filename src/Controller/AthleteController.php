<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Entity\Championship;
use App\Entity\Outsider;
use App\Form\AthleteType;
use App\Repository\AthleteRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/athlete")
 */
class AthleteController extends AbstractController
{
    /**
     * @Route("/", name="athlete_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(AthleteRepository $athleteRepository): Response
    {
        return $this->render('athlete/index.html.twig', [
            'athletes' => $athleteRepository->findBy(array(),array(),100,0),
        ]);
    }

    /**
     * @Route("/{id}", name="athlete_show", methods={"GET"})
     */
    public function show(Athlete $athlete): Response
    {
        $em = $this->getDoctrine()->getManager();
        $championships = $em->getRepository(Championship::class)->findAll();
        return $this->render('athlete/show.html.twig', [
            'athlete' => $athlete,
            'championships' => $championships
        ]);
    }

    /**
     * @Route("/outsider/{uid}", name="outsider_show", methods={"GET"})
     */
    public function outsiderShow(Request $request): Response
    {
        $uid = $request->get('uid');
        $em = $this->getDoctrine()->getManager();
        $outsiders = $em->getRepository(Outsider::class)->findByUid($uid);
        if (count($outsiders) < 1){
            $session = new Session();
            $session->getFlashBag()->add('warning','Aucun athlete trouvé pour cet identifiant');
            return $this->redirectToRoute('home');
        }
        return $this->render('outsider/show.html.twig', [
            'outsiders' => $outsiders
        ]);
    }

    /**
     * @Route("/{id}/edit", name="athlete_edit", methods={"GET","POST"})
     * @@IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Athlete $athlete): Response
    {
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('athlete_index', [
                'id' => $athlete->getId(),
            ]);
        }

        return $this->render('athlete/edit.html.twig', [
            'athlete' => $athlete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="athlete_delete", methods={"DELETE"})
     * @@IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, Athlete $athlete): Response
    {
        if ($this->isCsrfTokenValid('delete'.$athlete->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($athlete);
            $entityManager->flush();
        }

        return $this->redirectToRoute('athlete_index');
    }
}
