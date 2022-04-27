<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\PublicationType;
use App\Repository\Gestapp\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_publication_index', methods: ['GET'])]
    public function index(PublicationRepository $publicationRepository): Response
    {
        return $this->render('gestapp/publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PublicationRepository $publicationRepository): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_publication_show', methods: ['GET'])]
    public function show(Publication $publication): Response
    {
        return $this->render('gestapp/publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/showbyproperty/{idproperty}', name: 'op_admin_contact_showbyproperty', methods: ['GET'])]
    public function showByProperty(PublicationRepository $publicationRepository, $idProperty): Response
    {
        $publications = $publicationRepository->findBy(array('property' => $idProperty));
        return $this->render('gestapp/publication/showbyproperty.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            $publicationRepository->remove($publication);
        }

        return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
    }
}
