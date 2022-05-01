<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\PropertyType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/property')]
class PropertyController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('gestapp/property/index.html.twig', [
            'properties' => $propertyRepository->findBy(array('isIncreating' => 0)),
        ]);
    }

    #[Route('/inCreating', name: 'op_gestapp_property_inCreating', methods: ['GET'])]
    public function inCreating(PropertyRepository $propertyRepository): Response
    {
        return $this->render('gestapp/property/increating.html.twig', [
            'properties' => $propertyRepository->findBy(array('isIncreating' => 1)),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyRepository $propertyRepository): Response
    {
        $user = $this->getUser()->getId();

        $property = new Property();
        $property->setRefEmployed($user);
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/add', name:'op_gestapp_property_add', methods: ['GET', 'POST'])]
    public function add(
        PropertyRepository $propertyRepository,
        EmployedRepository $employedRepository,
        ComplementRepository $complementRepository,
        PublicationRepository $publicationRepository)
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $complement = new Complement();
        $complementRepository->add($complement);

        $publication = new Publication();
        $publicationRepository->add($publication);

        $property = new Property();
        $property->setName('Nouveau bien');
        $property->setRefEmployed($employed);
        $property->setOptions($complement);
        $property->setPublication($publication);
        $property->setIsIncreating(1);
        $propertyRepository->add($property);

        return $this->redirectToRoute('op_gestapp_property_firstedit', [
            'id' => $property->getId()
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        return $this->render('gestapp/property/show.html.twig', [
            'property' => $property,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        $complement = $property->getOptions();
        //dd($complement->getId());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/edit.html.twig', [
            'property' => $property,
            'idProperty' => $property->getId(),
            'complement' => $complement->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/firstedit', name: 'op_gestapp_property_firstedit', methods: ['GET', 'POST'])]
    public function firstedit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        $complement = $property->getOptions();
        //dd($complement->getId());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/firstedit.html.twig', [
            'property' => $property,
            'idProperty' => $property->getId(),
            'complement' => $complement->getId(),
            'publication' => $property->getPublication(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
            $propertyRepository->remove($property);
        }

        return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
    }
}
