<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyFamily;
use App\Form\Gestapp\choice\propertyFamilyType;
use App\Repository\Gestapp\choice\propertyFamilyRepository;
use App\Repository\Gestapp\choice\propertyRubricRepository;
use App\Repository\Gestapp\choice\propertyRubricssRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/family')]
class propertyFamilyController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_family_index', methods: ['GET'])]
    public function index(propertyFamilyRepository $propertyFamilyRepository): Response
    {
        return $this->render('gestapp/choice/property_family/index.html.twig', [
            'property_families' => $propertyFamilyRepository->findAll(),
        ]);
    }

    #[Route('/value/', name: 'app_gestapp_choice_property_family_value', methods: ['GET'])]
    public function value(propertyFamilyRepository $propertyFamilyRepository, propertyRubricRepository $rubricRepository, propertyRubricssRepository $rubricssRepository)
    {
        $families = $propertyFamilyRepository->value();
        $rubrics = $rubricRepository->value();
        $rubricss = $rubricssRepository->value();
        //dd($families);
        return $this->json([
            'code' => 200,
            'families' => $families,
            'rubrics' => $rubrics,
            'rubricss' => $rubricss
        ], 200);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_family_new', methods: ['GET', 'POST'])]
    public function new(Request $request, propertyFamilyRepository $propertyFamilyRepository): Response
    {
        $propertyFamily = new propertyFamily();
        $form = $this->createForm(propertyFamilyType::class, $propertyFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyFamilyRepository->add($propertyFamily, true);

            return $this->redirectToRoute('app_gestapp_choice_property_family_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_family/new.html.twig', [
            'property_family' => $propertyFamily,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_family_show', methods: ['GET'])]
    public function show(propertyFamily $propertyFamily): Response
    {
        return $this->render('gestapp/choice/property_family/show.html.twig', [
            'property_family' => $propertyFamily,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_family_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, propertyFamily $propertyFamily, propertyFamilyRepository $propertyFamilyRepository): Response
    {
        $form = $this->createForm(propertyFamilyType::class, $propertyFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyFamilyRepository->add($propertyFamily, true);

            return $this->redirectToRoute('app_gestapp_choice_property_family_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_family/edit.html.twig', [
            'property_family' => $propertyFamily,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_family_delete', methods: ['POST'])]
    public function delete(Request $request, propertyFamily $propertyFamily, propertyFamilyRepository $propertyFamilyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyFamily->getId(), $request->request->get('_token'))) {
            $propertyFamilyRepository->remove($propertyFamily, true);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_family_index', [], Response::HTTP_SEE_OTHER);
    }
}
