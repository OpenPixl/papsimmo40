<?php

namespace App\Controller\Admin;

use App\Entity\Admin\PdfRendered;
use App\Form\Admin\PdfRenderedType;
use App\Repository\Admin\PdfRenderedRepository;
use App\Repository\Webapp\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pdf/rendered')]
class PdfRenderedController extends AbstractController
{
    #[Route('/', name: 'app_admin_pdf_rendered_index', methods: ['GET'])]
    public function index(PdfRenderedRepository $pdfRenderedRepository): Response
    {
        return $this->render('admin/pdf_rendered/index.html.twig', [
            'pdf_rendereds' => $pdfRenderedRepository->findAll(),
        ]);
    }

    #[Route('/articletopdf/{name}', name: 'app_admin_pdf_rendered_articletopdf', methods: ['GET'])]
    public function articletopdf(PdfRenderedRepository $pdfRenderedRepository, $name, ArticlesRepository $articlesRepository): Response
    {
        $pdfRendered = $pdfRenderedRepository->findOneBy(['name' => $name]);

        $article = $articlesRepository->findOneBy(['slug'=> $name]);

        return $this->render('admin/pdf_rendered/articletopdf.html.twig', [
            'pdfrendered' => $pdfRendered,
            'article' =>$article
        ]);
    }

    #[Route('/pdfarticletohtml/{idarticle}', name: 'app_admin_pdf_rendered_pdfarticletohtml', methods: ['GET'])]
    public function pdfarticletohtml(PdfRenderedRepository $pdfRenderedRepository, ArticlesRepository $articlesRepository, $idarticle)
    {
        $article = $articlesRepository->find($idarticle);
        $pdfRendered = $pdfRenderedRepository->findOneBy(['name' => $article->getSlug()]);

        return $this->render('admin/pdf_rendered/show.html.twig', [
            'pdfrendered' => $pdfRendered,
        ]);
    }

    #[Route('/new', name: 'app_admin_pdf_rendered_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PdfRenderedRepository $pdfRenderedRepository): Response
    {
        $pdfRendered = new PdfRendered();
        $form = $this->createForm(PdfRenderedType::class, $pdfRendered);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfRenderedRepository->add($pdfRendered, true);

            return $this->redirectToRoute('app_admin_pdf_rendered_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/pdf_rendered/new.html.twig', [
            'pdf_rendered' => $pdfRendered,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_pdf_rendered_show', methods: ['GET'])]
    public function show(PdfRendered $pdfRendered): Response
    {
        return $this->render('admin/pdf_rendered/show.html.twig', [
            'pdf_rendered' => $pdfRendered,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_pdf_rendered_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PdfRendered $pdfRendered, PdfRenderedRepository $pdfRenderedRepository): Response
    {
        $form = $this->createForm(PdfRenderedType::class, $pdfRendered);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfRenderedRepository->add($pdfRendered, true);

            return $this->redirectToRoute('app_admin_pdf_rendered_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/pdf_rendered/edit.html.twig', [
            'pdf_rendered' => $pdfRendered,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_pdf_rendered_delete', methods: ['POST'])]
    public function delete(Request $request, PdfRendered $pdfRendered, PdfRenderedRepository $pdfRenderedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pdfRendered->getId(), $request->request->get('_token'))) {
            $pdfRenderedRepository->remove($pdfRendered, true);
        }

        return $this->redirectToRoute('app_admin_pdf_rendered_index', [], Response::HTTP_SEE_OTHER);
    }
}
