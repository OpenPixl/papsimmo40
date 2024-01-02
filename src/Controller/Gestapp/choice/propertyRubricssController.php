<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\propertyRubricss;
use App\Form\Gestapp\choice\propertyRubricssType;
use App\Repository\Gestapp\choice\propertyRubricssRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/property/rubricss')]
class propertyRubricssController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_rubricss_index', methods: ['GET'])]
    public function index(propertyRubricssRepository $propertyRubricssRepository): Response
    {
        return $this->render('gestapp/choice/property_rubricss/index.html.twig', [
            'property_rubricsses' => $propertyRubricssRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_rubricss_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $propertyRubricss = new propertyRubricss();
        $form = $this->createForm(propertyRubricssType::class, $propertyRubricss);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($propertyRubricss);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_choice_property_rubricss_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/choice/property_rubricss/new.html.twig', [
            'property_rubricss' => $propertyRubricss,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_rubricss_show', methods: ['GET'])]
    public function show(propertyRubricss $propertyRubricss): Response
    {
        return $this->render('gestapp/choice/property_rubricss/show.html.twig', [
            'property_rubricss' => $propertyRubricss,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_rubricss_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, propertyRubricss $propertyRubricss, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(propertyRubricssType::class, $propertyRubricss);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_choice_property_rubricss_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/choice/property_rubricss/edit.html.twig', [
            'property_rubricss' => $propertyRubricss,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_rubricss_delete', methods: ['POST'])]
    public function delete(Request $request, propertyRubricss $propertyRubricss, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyRubricss->getId(), $request->request->get('_token'))) {
            $entityManager->remove($propertyRubricss);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestapp_choice_property_rubricss_index', [], Response::HTTP_SEE_OTHER);
    }
}
