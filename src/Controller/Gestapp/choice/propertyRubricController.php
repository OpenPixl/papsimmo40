<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyRubric;
use App\Form\Gestapp\choice\propertyRubricType;
use App\Repository\Gestapp\choice\propertyRubricRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/rubric')]
class propertyRubricController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_rubric_index', methods: ['GET'])]
    public function index(propertyRubricRepository $propertyRubricRepository): Response
    {
        return $this->render('gestapp/choice/property_rubric/index.html.twig', [
            'property_rubrics' => $propertyRubricRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_rubric_new', methods: ['GET', 'POST'])]
    public function new(Request $request, propertyRubricRepository $propertyRubricRepository): Response
    {
        $propertyRubric = new propertyRubric();
        $form = $this->createForm(propertyRubricType::class, $propertyRubric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRubricRepository->add($propertyRubric, true);

            return $this->redirectToRoute('app_gestapp_choice_property_rubric_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_rubric/new.html.twig', [
            'property_rubric' => $propertyRubric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_rubric_show', methods: ['GET'])]
    public function show(propertyRubric $propertyRubric): Response
    {
        return $this->render('gestapp/choice/property_rubric/show.html.twig', [
            'property_rubric' => $propertyRubric,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_rubric_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, propertyRubric $propertyRubric, propertyRubricRepository $propertyRubricRepository): Response
    {
        $form = $this->createForm(propertyRubricType::class, $propertyRubric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRubricRepository->add($propertyRubric, true);

            return $this->redirectToRoute('app_gestapp_choice_property_rubric_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_rubric/edit.html.twig', [
            'property_rubric' => $propertyRubric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_rubric_delete', methods: ['POST'])]
    public function delete(Request $request, propertyRubric $propertyRubric, propertyRubricRepository $propertyRubricRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyRubric->getId(), $request->request->get('_token'))) {
            $propertyRubricRepository->remove($propertyRubric, true);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_rubric_index', [], Response::HTTP_SEE_OTHER);
    }
}
