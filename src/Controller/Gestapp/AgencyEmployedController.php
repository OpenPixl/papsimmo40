<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\AgencyEmployed;
use App\Form\Gestapp\AgencyEmployedType;
use App\Repository\Gestapp\AgencyEmployedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/agencyemployed')]
class AgencyEmployedController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_agencyemployed_index', methods: ['GET'])]
    public function index(AgencyEmployedRepository $agencyEmployedRepository): Response
    {
        $agencyEmployed = $agencyEmployedRepository->findAll();
        return $this->json([
            "code" => 200,
            "view" => $this->renderView('gestapp/agencyemployed/index.html.twig', [
                'agencyemployeds' => $agencyEmployed
            ]),
        ], 200);
    }

    #[Route('/new', name: 'app_gestapp_agency_employed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agencyEmployed = new AgencyEmployed();
        $form = $this->createForm(AgencyEmployedType::class, $agencyEmployed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($agencyEmployed);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_agency_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/agency_employed/new.html.twig', [
            'agency_employed' => $agencyEmployed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_agency_employed_show', methods: ['GET'])]
    public function show(AgencyEmployed $agencyEmployed): Response
    {
        return $this->render('gestapp/agency_employed/show.html.twig', [
            'agency_employed' => $agencyEmployed,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_agency_employed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AgencyEmployed $agencyEmployed, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AgencyEmployedType::class, $agencyEmployed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_agency_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/agency_employed/edit.html.twig', [
            'agency_employed' => $agencyEmployed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_agency_employed_delete', methods: ['POST'])]
    public function delete(Request $request, AgencyEmployed $agencyEmployed, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agencyEmployed->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($agencyEmployed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestapp_agency_employed_index', [], Response::HTTP_SEE_OTHER);
    }
}
