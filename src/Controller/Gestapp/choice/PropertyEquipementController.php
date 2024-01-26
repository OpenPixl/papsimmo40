<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyEquipement;
use App\Form\Gestapp\choice\PropertyEquipementType;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\ComplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/choice/property/equipement')]
class PropertyEquipementController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_equipement_index', methods: ['GET'])]
    public function index(PropertyEquipementRepository $propertyEquipementRepository): Response
    {
        return $this->render('gestapp/choice/property_equipement/index.html.twig', [
            'property_equipements' => $propertyEquipementRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_gestapp_choice_property_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyEquipementRepository $propertyEquipementRepository, ComplementRepository $complementRepository): Response
    {
        $propertyEquipement = new PropertyEquipement();
        $form = $this->createForm(PropertyEquipementType::class, $propertyEquipement, [
            'action' => $this->generateUrl('app_gestapp_choice_property_equipement_new2'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEquipementRepository->add($propertyEquipement);
            return $this->redirectToRoute('app_gestapp_choice_property_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_equipement/new.html.twig', [
            'property_equipement' => $propertyEquipement,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_gestapp_choice_property_equipement_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, PropertyEquipementRepository $propertyEquipementRepository): Response
    {
        $propertyEquipement = new PropertyEquipement();
        $form = $this->createForm(PropertyEquipementType::class, $propertyEquipement, [
            'action' => $this->generateUrl('app_gestapp_choice_property_equipement_new2'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEquipementRepository->add($propertyEquipement);
            return $this->json([
                'code' => 200,
                'equip' => $propertyEquipement->getName(),
                'valueequip' => $propertyEquipement->getId(),
                'message' => "Une nouvelle orientation a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_equipement/new.html.twig', [
            'property_equipement' => $propertyEquipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_equipement_show', methods: ['GET'])]
    public function show(PropertyEquipement $propertyEquipement): Response
    {
        return $this->render('gestapp/choice/property_equipement/show.html.twig', [
            'property_equipement' => $propertyEquipement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyEquipement $propertyEquipement, PropertyEquipementRepository $propertyEquipementRepository): Response
    {
        $form = $this->createForm(PropertyEquipementType::class, $propertyEquipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEquipementRepository->add($propertyEquipement);
            return $this->redirectToRoute('app_gestapp_choice_property_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_equipement/edit.html.twig', [
            'property_equipement' => $propertyEquipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyEquipement $propertyEquipement, PropertyEquipementRepository $propertyEquipementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyEquipement->getId(), $request->request->get('_token'))) {
            $propertyEquipementRepository->remove($propertyEquipement);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_equipement_index', [], Response::HTTP_SEE_OTHER);
    }
}
