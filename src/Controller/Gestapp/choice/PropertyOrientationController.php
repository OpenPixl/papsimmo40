<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyOrientation;
use App\Form\Gestapp\choice\PropertyOrientationType;
use App\Repository\Gestapp\choice\PropertyOrientationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/orientation')]
class PropertyOrientationController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_orientation_index', methods: ['GET'])]
    public function index(PropertyOrientationRepository $propertyOrientationRepository): Response
    {
        return $this->render('gestapp/choice/property_orientation/index.html.twig', [
            'property_orientations' => $propertyOrientationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_orientation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyOrientationRepository $propertyOrientationRepository): Response
    {
        $propertyOrientation = new PropertyOrientation();
        $form = $this->createForm(PropertyOrientationType::class, $propertyOrientation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyOrientationRepository->add($propertyOrientation);
            return $this->redirectToRoute('app_gestapp_choice_property_orientation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_orientation/new.html.twig', [
            'property_orientation' => $propertyOrientation,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_gestapp_choice_property_orientation_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, PropertyOrientationRepository $propertyOrientationRepository): Response
    {
        $propertyOrientation = new PropertyOrientation();
        $form = $this->createForm(PropertyOrientationType::class, $propertyOrientation,[
            'action' => $this->generateUrl('app_gestapp_choice_property_orientation_new2'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyOrientationRepository->add($propertyOrientation);
            return $this->json([
                'code' => 200,
                'orient' => $propertyOrientation->getName(),
                'valueorient' => $propertyOrientation->getId(),
                'message' => "Une nouvelle orientation a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_orientation/new.html.twig', [
            'property_orientation' => $propertyOrientation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_orientation_show', methods: ['GET'])]
    public function show(PropertyOrientation $propertyOrientation): Response
    {
        return $this->render('gestapp/choice/property_orientation/show.html.twig', [
            'property_orientation' => $propertyOrientation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_orientation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyOrientation $propertyOrientation, PropertyOrientationRepository $propertyOrientationRepository): Response
    {
        $form = $this->createForm(PropertyOrientationType::class, $propertyOrientation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyOrientationRepository->add($propertyOrientation);
            return $this->redirectToRoute('app_gestapp_choice_property_orientation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_orientation/edit.html.twig', [
            'property_orientation' => $propertyOrientation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_orientation_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyOrientation $propertyOrientation, PropertyOrientationRepository $propertyOrientationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyOrientation->getId(), $request->request->get('_token'))) {
            $propertyOrientationRepository->remove($propertyOrientation);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_orientation_index', [], Response::HTTP_SEE_OTHER);
    }
}
