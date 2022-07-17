<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Form\Gestapp\choice\PropertyEnergyType;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/energy')]
class PropertyEnergyController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_energy_index', methods: ['GET'])]
    public function index(PropertyEnergyRepository $propertyEnergyRepository): Response
    {
        return $this->render('gestapp/choice/property_energy/index.html.twig', [
            'property_energies' => $propertyEnergyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_energy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyEnergyRepository $propertyEnergyRepository): Response
    {
        $propertyEnergy = new PropertyEnergy();
        $form = $this->createForm(PropertyEnergyType::class, $propertyEnergy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEnergyRepository->add($propertyEnergy);
            return $this->redirectToRoute('app_gestapp_choice_property_energy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_energy/new.html.twig', [
            'property_energy' => $propertyEnergy,
            'form' => $form,
        ]);
    }
    #[Route('/new2', name: 'app_gestapp_choice_property_energy_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, PropertyEnergyRepository $propertyEnergyRepository): Response
    {
        $propertyEnergy = new PropertyEnergy();
        $form = $this->createForm(PropertyEnergyType::class, $propertyEnergy,[
            'action' => $this->generateUrl('app_gestapp_choice_property_energy_new2'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEnergyRepository->add($propertyEnergy);
            return $this->json([
                'code' => 200,
                'energy' => $propertyEnergy->getName(),
                'valueenergy' => $propertyEnergy->getId(),
                'message' => "Une nouvelle source a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_energy/new.html.twig', [
            'property_energy' => $propertyEnergy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_energy_show', methods: ['GET'])]
    public function show(PropertyEnergy $propertyEnergy): Response
    {
        return $this->render('gestapp/choice/property_energy/show.html.twig', [
            'property_energy' => $propertyEnergy,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_energy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyEnergy $propertyEnergy, PropertyEnergyRepository $propertyEnergyRepository): Response
    {
        $form = $this->createForm(PropertyEnergyType::class, $propertyEnergy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyEnergyRepository->add($propertyEnergy);
            return $this->redirectToRoute('app_gestapp_choice_property_energy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_energy/edit.html.twig', [
            'property_energy' => $propertyEnergy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_energy_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyEnergy $propertyEnergy, PropertyEnergyRepository $propertyEnergyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyEnergy->getId(), $request->request->get('_token'))) {
            $propertyEnergyRepository->remove($propertyEnergy);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_energy_index', [], Response::HTTP_SEE_OTHER);
    }
}
