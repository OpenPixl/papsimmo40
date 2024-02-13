<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Form\Gestapp\choice\PropertyDefinitionType;
use App\Repository\Gestapp\choice\PropertyDefinitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/choice/property/definition')]
class PropertyDefinitionController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_choice_property_definition_index', methods: ['GET'])]
    public function index(PropertyDefinitionRepository $propertyDefinitionRepository): Response
    {
        return $this->render('gestapp/choice/property_definition/index.html.twig', [
            'property_definitions' => $propertyDefinitionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_choice_property_definition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyDefinitionRepository $propertyDefinitionRepository): Response
    {
        $propertyDefinition = new PropertyDefinition();
        $form = $this->createForm(PropertyDefinitionType::class, $propertyDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyDefinitionRepository->add($propertyDefinition);
            return $this->redirectToRoute('app_gestapp_choice_property_definition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_definition/new.html.twig', [
            'property_definition' => $propertyDefinition,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'op_gestapp_choice_property_definition_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, PropertyDefinitionRepository $propertyDefinitionRepository): Response
    {
        $propertyDefinition = new PropertyDefinition();
        $form = $this->createForm(PropertyDefinitionType::class, $propertyDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyDefinitionRepository->add($propertyDefinition);
            return $this->redirectToRoute('op_gestapp_choice_property_definition_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyDefinitionRepository->add($propertyDefinition);
            return $this->json([
                'code' => 200,
                'propertyDef' => $propertyDefinition->getName(),
                'valuepropertyDef' => $propertyDefinition->getId(),
                'message' => "Un nouveau type de bien a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_definition/new.html.twig', [
            'property_definition' => $propertyDefinition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_definition_show', methods: ['GET'])]
    public function show(PropertyDefinition $propertyDefinition): Response
    {
        return $this->render('gestapp/choice/property_definition/show.html.twig', [
            'property_definition' => $propertyDefinition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_definition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyDefinition $propertyDefinition, PropertyDefinitionRepository $propertyDefinitionRepository): Response
    {
        $form = $this->createForm(PropertyDefinitionType::class, $propertyDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyDefinitionRepository->add($propertyDefinition);
            return $this->redirectToRoute('app_gestapp_choice_property_definition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_definition/edit.html.twig', [
            'property_definition' => $propertyDefinition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_definition_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyDefinition $propertyDefinition, PropertyDefinitionRepository $propertyDefinitionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyDefinition->getId(), $request->request->get('_token'))) {
            $propertyDefinitionRepository->remove($propertyDefinition);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_definition_index', [], Response::HTTP_SEE_OTHER);
    }
}
