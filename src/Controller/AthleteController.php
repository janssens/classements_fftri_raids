<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Form\AthleteType;
use App\Repository\AthleteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/athlete")
 */
class AthleteController extends AbstractController
{
    /**
     * @Route("/", name="athlete_index", methods={"GET"})
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
        return $this->render('athlete/show.html.twig', [
            'athlete' => $athlete,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="athlete_edit", methods={"GET","POST"})
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
