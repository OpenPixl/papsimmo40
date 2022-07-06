<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyTypology;
use App\Form\Gestapp\choice\PropertyTypologyType;
use App\Repository\Gestapp\choice\PropertyTypologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/typology')]
class PropertyTypologyController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_typology_index', methods: ['GET'])]
    public function index(PropertyTypologyRepository $propertyTypologyRepository): Response
    {
        return $this->render('gestapp/choice/property_typology/index.html.twig', [
            'property_typologies' => $propertyTypologyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_typology_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyTypologyRepository $propertyTypologyRepository): Response
    {
        $propertyTypology = new PropertyTypology();
        $form = $this->createForm(PropertyTypologyType::class, $propertyTypology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyTypologyRepository->add($propertyTypology);
            return $this->redirectToRoute('app_gestapp_choice_property_typology_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_typology/new.html.twig', [
            'property_typology' => $propertyTypology,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_typology_show', methods: ['GET'])]
    public function show(PropertyTypology $propertyTypology): Response
    {
        return $this->render('gestapp/choice/property_typology/show.html.twig', [
            'property_typology' => $propertyTypology,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_typology_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyTypology $propertyTypology, PropertyTypologyRepository $propertyTypologyRepository): Response
    {
        $form = $this->createForm(PropertyTypologyType::class, $propertyTypology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyTypologyRepository->add($propertyTypology);
            return $this->redirectToRoute('app_gestapp_choice_property_typology_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_typology/edit.html.twig', [
            'property_typology' => $propertyTypology,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_typology_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyTypology $propertyTypology, PropertyTypologyRepository $propertyTypologyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyTypology->getId(), $request->request->get('_token'))) {
            $propertyTypologyRepository->remove($propertyTypology);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_typology_index', [], Response::HTTP_SEE_OTHER);
    }
}
