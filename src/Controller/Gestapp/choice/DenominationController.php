<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\Denomination;
use App\Form\Gestapp\choice\DenominationType;
use App\Repository\Gestapp\choice\DenominationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/choice/denomination')]
class DenominationController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_denomination_index', methods: ['GET'])]
    public function index(DenominationRepository $denominationRepository): Response
    {
        return $this->render('gestapp/choice/denomination/index.html.twig', [
            'denominations' => $denominationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_denomination_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DenominationRepository $denominationRepository): Response
    {
        $denomination = new Denomination();
        $form = $this->createForm(DenominationType::class, $denomination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $denominationRepository->add($denomination);
            return $this->redirectToRoute('app_gestapp_choice_denomination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/denomination/new.html.twig', [
            'denomination' => $denomination,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_gestapp_choice_denomination_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, DenominationRepository $denominationRepository): Response
    {
        $denomination = new Denomination();
        $form = $this->createForm(DenominationType::class, $denomination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $denominationRepository->add($denomination);
            return $this->json([
                'code' => 200,
                'cat' => $denomination->getName(),
                'valuecat'=> $denomination->getId(),
                'message' => "Une nouvelle catégorie a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/denomination/new.html.twig', [
            'denomination' => $denomination,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_gestapp_choice_denomination_show', methods: ['GET'])]
    public function show(Denomination $denomination): Response
    {
        return $this->render('gestapp/choice/denomination/show.html.twig', [
            'denomination' => $denomination,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_denomination_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Denomination $denomination, DenominationRepository $denominationRepository): Response
    {
        $form = $this->createForm(DenominationType::class, $denomination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $denominationRepository->add($denomination);
            return $this->redirectToRoute('app_gestapp_choice_denomination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/denomination/edit.html.twig', [
            'denomination' => $denomination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_denomination_delete', methods: ['POST'])]
    public function delete(Request $request, Denomination $denomination, DenominationRepository $denominationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$denomination->getId(), $request->request->get('_token'))) {
            $denominationRepository->remove($denomination);
        }

        return $this->redirectToRoute('app_gestapp_choice_denomination_index', [], Response::HTTP_SEE_OTHER);
    }
}
