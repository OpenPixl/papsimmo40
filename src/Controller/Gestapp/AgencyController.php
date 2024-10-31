<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Agency;
use App\Form\Gestapp\AgencyType;
use App\Repository\Gestapp\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/agency')]
class AgencyController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_agency_index', methods: ['GET'])]
    public function index(AgencyRepository $agencyRepository): Response
    {
        $agencies = $agencyRepository->findAll();
        return $this->json([
            "code" => 200,
            "view" => $this->renderView('gestapp/agency/index.html.twig', [
                'agencies' => $agencies
            ]),
        ], 200);
    }

    #[Route('/new', name: 'op_gestapp_agency_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agency = new Agency();
        $form = $this->createForm(AgencyType::class, $agency, [
            'action' => $this->generateUrl('op_gestapp_agency_new'),
            'method' => 'POST',
            'attr' => [
                'id' => 'formAgency',
            ]
        ]);
        $form->handleRequest($request);

        $agencies = $entityManager->getRepository(Agency::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($agency);
            $entityManager->flush();

            $agencies = $entityManager->getRepository(Agency::class)->findAll();

            return $this->json([
                "code" => 200,
                "message" => "Les modifications à la recommandations ont étés correctement apportées.",
                'liste' => $this->renderView('gestapp/agency/include/_liste.html.twig',[
                    'agencies' => $agencies
                ])
            ],200);
        }

        //dd($request->request->all());

        // view
        $view = $this->render('gestapp/agency/new.html.twig', [
            'agencies' => $agencies,
            'agency' => $agency,
            'form' => $form,
        ]);

        // return
        return $this->json([
            "code" => 200,
            'view' => $view->getContent()
        ], 200);
    }

    #[Route('/{id}', name: 'app_gestapp_agency_show', methods: ['GET'])]
    public function show(Agency $agency): Response
    {
        return $this->render('gestapp/agency/show.html.twig', [
            'agency' => $agency,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_agency_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agency $agency, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_agency_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/agency/edit.html.twig', [
            'agency' => $agency,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_agency_delete', methods: ['POST'])]
    public function delete(Request $request, Agency $agency, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agency->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($agency);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_agency_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/del', name: 'op_gestapp_agency_del', methods: ['POST'])]
    public function del(Request $request, Agency $agency, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($agency);
        $entityManager->flush();

        $agency = new Agency();
        $form = $this->createForm(AgencyType::class, $agency, [
            'action' => $this->generateUrl('op_gestapp_agency_new'),
            'method' => 'POST',
            'attr' => [
                'id' => 'formAgency',
            ]
        ]);
        $form->handleRequest($request);

        $agencies = $entityManager->getRepository(Agency::class)->findAll();

        // view
        $view = $this->render('gestapp/agency/new.html.twig', [
            'agencies' => $agencies,
            'agency' => $agency,
            'form' => $form,
        ]);

        // return
        return $this->json([
            "code" => 200,
            'view' => $view->getContent()
        ], 200);
    }

}
