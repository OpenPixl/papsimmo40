<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyState;
use App\Form\Gestapp\choice\PropertyStateType;
use App\Repository\Gestapp\choice\PropertyStateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/state')]
class PropertyStateController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_choice_property_state_index', methods: ['GET'])]
    public function index(PropertyStateRepository $propertyStateRepository): Response
    {
        return $this->render('gestapp/choice/property_state/index.html.twig', [
            'property_states' => $propertyStateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_choice_property_state_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyStateRepository $propertyStateRepository): Response
    {
        $propertyState = new PropertyState();
        $form = $this->createForm(PropertyStateType::class, $propertyState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyStateRepository->add($propertyState);
            return $this->redirectToRoute('op_gestapp_choice_property_state_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_state/new.html.twig', [
            'property_state' => $propertyState,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'op_gestapp_choice_property_state_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, PropertyStateRepository $propertyStateRepository): Response
    {

        $propertyState = new PropertyState();
        $form = $this->createForm(PropertyStateType::class, $propertyState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyStateRepository->add($propertyState);
            $state = $propertyState->getName();
            $valuestate = $propertyState->getId();
            return $this->json([
                'code' => 200,
                'state' => $state,
                'valuestate' => $valuestate,
                'message' => "Un nouvel état a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_state/new.html.twig', [
            'property_state' => $propertyState,
            'form' => $form,
        ]);

    }

    #[Route('/{id}', name: 'op_gestapp_choice_property_state_show', methods: ['GET'])]
    public function show(PropertyState $propertyState): Response
    {
        return $this->render('gestapp/choice/property_state/show.html.twig', [
            'property_state' => $propertyState,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_choice_property_state_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyState $propertyState, PropertyStateRepository $propertyStateRepository): Response
    {
        $form = $this->createForm(PropertyStateType::class, $propertyState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyStateRepository->add($propertyState);
            return $this->redirectToRoute('op_gestapp_choice_property_state_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_state/edit.html.twig', [
            'property_state' => $propertyState,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_choice_property_state_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyState $propertyState, PropertyStateRepository $propertyStateRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyState->getId(), $request->request->get('_token'))) {
            $propertyStateRepository->remove($propertyState);
        }

        return $this->redirectToRoute('op_gestapp_choice_property_state_index', [], Response::HTTP_SEE_OTHER);
    }
}
