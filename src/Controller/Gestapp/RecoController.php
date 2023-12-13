<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Reco;
use App\Form\Gestapp\RecoType;
use App\Repository\Gestapp\RecoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/reco')]
class RecoController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_reco_index', methods: ['GET'])]
    public function index(RecoRepository $recoRepository): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true)
        {
            $recos = $recoRepository->findAll();
            return $this->render('gestapp/reco/index.html.twig', [
                'recos' => $recos,
            ]);
        }else{
            $recos = $recoRepository->findBy($user->getId());
            return $this->render('gestapp/reco/index.html.twig', [
                'recos' => $recos,
            ]);
        }
    }

    #[Route('/new', name: 'op_gestapp_reco_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reco = new Reco();
        $form = $this->createForm(RecoType::class, $reco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reco);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/reco/new.html.twig', [
            'reco' => $reco,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_reco_show', methods: ['GET'])]
    public function show(Reco $reco): Response
    {
        return $this->render('gestapp/reco/show.html.twig', [
            'reco' => $reco,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_reco_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reco $reco, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecoType::class, $reco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/reco/edit.html.twig', [
            'reco' => $reco,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_reco_delete', methods: ['POST'])]
    public function delete(Request $request, Reco $reco, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reco->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reco);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
    }
}
