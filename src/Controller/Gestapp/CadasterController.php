<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Cadaster;
use App\Form\Gestapp\CadasterType;
use App\Repository\Gestapp\CadasterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/cadaster')]
class CadasterController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_cadaster_index', methods: ['GET'])]
    public function index(CadasterRepository $cadasterRepository): Response
    {
        return $this->render('gestapp/cadaster/index.html.twig', [
            'cadasters' => $cadasterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_cadaster_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CadasterRepository $cadasterRepository): Response
    {
        $cadaster = new Cadaster();
        $form = $this->createForm(CadasterType::class, $cadaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cadasterRepository->add($cadaster, true);

            return $this->redirectToRoute('app_gestapp_cadaster_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/cadaster/new.html.twig', [
            'cadaster' => $cadaster,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_cadaster_show', methods: ['GET'])]
    public function show(Cadaster $cadaster): Response
    {
        return $this->render('gestapp/cadaster/show.html.twig', [
            'cadaster' => $cadaster,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_cadaster_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cadaster $cadaster, CadasterRepository $cadasterRepository): Response
    {
        $form = $this->createForm(CadasterType::class, $cadaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cadasterRepository->add($cadaster, true);

            return $this->redirectToRoute('app_gestapp_cadaster_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/cadaster/edit.html.twig', [
            'cadaster' => $cadaster,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_cadaster_delete', methods: ['POST'])]
    public function delete(Request $request, Cadaster $cadaster, CadasterRepository $cadasterRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cadaster->getId(), $request->request->get('_token'))) {
            $cadasterRepository->remove($cadaster, true);
        }

        return $this->redirectToRoute('app_gestapp_cadaster_index', [], Response::HTTP_SEE_OTHER);
    }
}
