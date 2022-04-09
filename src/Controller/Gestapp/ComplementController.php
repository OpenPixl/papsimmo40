<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Form\Gestapp\ComplementType;
use App\Repository\Gestapp\ComplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/complement')]
class ComplementController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_complement_index', methods: ['GET'])]
    public function index(ComplementRepository $complementRepository): Response
    {
        return $this->render('gestapp/complement/index.html.twig', [
            'complements' => $complementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ComplementRepository $complementRepository): Response
    {
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complementRepository->add($complement);
            return $this->redirectToRoute('app_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/complement/new.html.twig', [
            'complement' => $complement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_complement_show', methods: ['GET'])]
    public function show(Complement $complement): Response
    {
        return $this->render('gestapp/complement/show.html.twig', [
            'complement' => $complement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Complement $complement, ComplementRepository $complementRepository): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complementRepository->add($complement);
            return $this->redirectToRoute('app_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/complement/edit.html.twig', [
            'complement' => $complement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_complement_delete', methods: ['POST'])]
    public function delete(Request $request, Complement $complement, ComplementRepository $complementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$complement->getId(), $request->request->get('_token'))) {
            $complementRepository->remove($complement);
        }

        return $this->redirectToRoute('app_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
    }
}
