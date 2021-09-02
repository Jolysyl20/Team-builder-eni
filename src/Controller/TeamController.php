<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Personne;
use App\Form\EquipeType;
use App\Form\PersonneType;
use App\Repository\EquipeRepository;
use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/team")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/", name="team_index", methods={"POST","GET"})
     */
    public function index(Request $request, EquipeRepository $equipeRepository, PersonneRepository $PersonneRepository): Response
    {
        // formulaire personne
        $personne = new Personne();
        $formPersonne = $this->createForm(PersonneType::class, $personne);
        $formPersonne->handleRequest($request);
        if ($formPersonne->isSubmitted()) {
            $equipe = $formPersonne->get('equipes')->getData();
            $personne->addEquipe($equipe);
            $em = $this->getDoctrine()->getManager();
            $em->persist($personne);
            $em->flush();
        }

        $equipe = new Equipe();
        $formEquipe = $this->createForm(EquipeType::class, $equipe);
        $formEquipe->handleRequest($request);
        if ($formEquipe->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipe);
            $entityManager->flush();
        }

        return $this->render('team/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
            'personnes' => $PersonneRepository->findAll(),
            'formEquipe' => $formEquipe->createView(),
            'formPersonne'=> $formPersonne->createView()
        ]);
    }



    /**
     * @Route("/{id}", name="team_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        return $this->render('team/show.html.twig', [
            'equipe' => $equipe,
            
        ]);
    }

    /**
     * @Route("/f", name="team_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Equipe $personne): Response
    {
        $form = $this->createForm(EquipeType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/edit.html.twig', [
            'equipe' => $personne,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="team_delete", methods={"POST"})
     */
    public function delete(Personne $personne,Request $request ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personne->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($personne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/{id}", name="equipe_delete", methods={"POST"})
     */
    public function deleteEquipe(Equipe $equipe,Personne $personne ,Request $request ): Response
    {
        if ($this->isCsrfTokenValid('deleteEquipe'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipe);
            $entityManager->remove($personne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }

}
