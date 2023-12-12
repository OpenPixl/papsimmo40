<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\CatDocument;
use App\Form\Gestapp\choice\CatDocumentType;
use App\Repository\Gestapp\choice\CatDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/choice/cat/document')]
class CatDocumentController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_cat_document_index', methods: ['GET'])]
    public function index(CatDocumentRepository $catDocumentRepository): Response
    {
            $categories = $catDocumentRepository->findAll();
            return $this->render('gestapp/document/categories.html.twig', [
                'categories' => $categories,
            ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_cat_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CatDocumentRepository $catDocumentRepository): Response
    {
        $catDocument = new CatDocument();
        $form = $this->createForm(CatDocumentType::class, $catDocument,[
            'action' => $this->generateUrl('app_gestapp_choice_cat_document_new'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $catDocumentRepository->add($catDocument, true);

            return $this->json([
                'code' => 200,
                'data' => $catDocument->getName(),
                'value'=> $catDocument->getId(),
                'message' => "Catégorie ajoutée à la base."
            ]);
        }

        return $this->renderForm('gestapp/choice/cat_document/new.html.twig', [
            'cat_document' => $catDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_cat_document_show', methods: ['GET'])]
    public function show(CatDocument $catDocument): Response
    {
        return $this->render('gestapp/choice/cat_document/show.html.twig', [
            'cat_document' => $catDocument,
        ]);
    }

    #[Route('/json/{json}', name: 'app_gestapp_choice_cat_document_listcat', methods: ['GET', 'POST'])]
    public function listcat(CatDocumentRepository $catDocumentRepository, $json): Response
    {
        if($json == 0){
            $categories = $catDocumentRepository->findAll();
            return $this->render('gestapp/document/categories.html.twig', [
                'categories' => $categories,
            ]);
        }else{
            $categories = $catDocumentRepository->findAll();
            return $this->json([
                'code' => 200,
                'liste' => $this->renderView('gestapp/document/categories.html.twig', [
                    'categories' => $categories,
                ]),
                'message' => 'Ok'
            ], 200);
        }
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_cat_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CatDocument $catDocument, CatDocumentRepository $catDocumentRepository): Response
    {
        $form = $this->createForm(CatDocumentType::class, $catDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $catDocumentRepository->add($catDocument, true);

            return $this->redirectToRoute('app_gestapp_choice_cat_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/cat_document/edit.html.twig', [
            'cat_document' => $catDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_cat_document_delete', methods: ['POST'])]
    public function delete(Request $request, CatDocument $catDocument, CatDocumentRepository $catDocumentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$catDocument->getId(), $request->request->get('_token'))) {
            $catDocumentRepository->remove($catDocument, true);
        }

        return $this->redirectToRoute('app_gestapp_choice_cat_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
