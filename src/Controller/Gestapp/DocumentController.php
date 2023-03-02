<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Document;
use App\Form\Gestapp\DocumentType;
use App\Repository\Gestapp\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/document')]
class DocumentController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_document_index', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render('gestapp/document/index.html.twig', [
            'documents' => $documentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DocumentRepository $documentRepository): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->add($document, true);

            return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/document/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'op_gestapp_document_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, DocumentRepository $documentRepository): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->add($document, true);

            return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/document/new2.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render('gestapp/document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->add($document, true);

            return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $documentRepository->remove($document, true);
        }

        return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
