<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\OtherOption;
use App\Form\Gestapp\choice\OtherOptionType;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/choice/other/option')]
class OtherOptionController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_other_option_index', methods: ['GET'])]
    public function index(OtherOptionRepository $otherOptionRepository): Response
    {
        return $this->render('gestapp/choice/other_option/index.html.twig', [
            'other_options' => $otherOptionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_other_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OtherOptionRepository $otherOptionRepository): Response
    {
        $otherOption = new OtherOption();
        $form = $this->createForm(OtherOptionType::class, $otherOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otherOptionRepository->add($otherOption);
            return $this->redirectToRoute('app_gestapp_choice_other_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/other_option/new.html.twig', [
            'other_option' => $otherOption,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_gestapp_choice_other_option_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, OtherOptionRepository $otherOptionRepository): Response
    {
        $otherOption = new OtherOption();
        $form = $this->createForm(OtherOptionType::class, $otherOption, [
            'action'=>$this->generateUrl('app_gestapp_choice_other_option_new2'),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otherOptionRepository->add($otherOption);
            return $this->json([
                'code' => 200,
                'other' => $otherOption->getName(),
                'valueother' => $otherOption->getId(),
                'message' => "Une nouvelle source a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/other_option/new.html.twig', [
            'other_option' => $otherOption,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_other_option_show', methods: ['GET'])]
    public function show(OtherOption $otherOption): Response
    {
        return $this->render('gestapp/choice/other_option/show.html.twig', [
            'other_option' => $otherOption,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_other_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OtherOption $otherOption, OtherOptionRepository $otherOptionRepository): Response
    {
        $form = $this->createForm(OtherOptionType::class, $otherOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otherOptionRepository->add($otherOption);
            return $this->redirectToRoute('app_gestapp_choice_other_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/other_option/edit.html.twig', [
            'other_option' => $otherOption,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_other_option_delete', methods: ['POST'])]
    public function delete(Request $request, OtherOption $otherOption, OtherOptionRepository $otherOptionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$otherOption->getId(), $request->request->get('_token'))) {
            $otherOptionRepository->remove($otherOption);
        }

        return $this->redirectToRoute('app_gestapp_choice_other_option_index', [], Response::HTTP_SEE_OTHER);
    }
}
