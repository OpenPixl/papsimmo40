<?php

namespace App\Controller\Webapp;

use App\Entity\Webapp\Articles;
use App\Form\Webapp\ArticlesType;
use App\Repository\Webapp\ArticlesRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/webapp/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'op_webapp_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('webapp/articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_webapp_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticlesRepository $articlesRepository): Response
    {
        $user = $this->getUser();

        $article = new Articles();
        $article->setAuthor($user);
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_new'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articlesRepository->add($article);
            return $this->json([
                'code' => 200,
                'message' => "L'article a été créer"
            ], 200);
        }

        return $this->renderForm('webapp/articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_webapp_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('webapp/articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_webapp_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_edit', ['id'=> $article->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articlesRepository->add($article);
            return $this->json([
                'code' => 200,
                'message' => "L'article a été modifié."
            ], 200);
        }

        return $this->renderForm('webapp/articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_webapp_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articlesRepository->remove($article);
        }
        return $this->redirectToRoute('op_webapp_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}', name: 'op_webapp_articles_del', methods: ['POST'])]
    public function del(Request $request, Articles $articles,ArticlesRepository $articlesRepository)
    {
        $articlesRepository->remove($articles);

        $listarticles = $articlesRepository->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'liste' => $this->renderView('webapp/articles/include/_liste.html.twig', [
                'articles' => $listarticles
            ])
        ], 200);
    }

    #[Route('/fivelstproperty', name: 'op_webapp_articles_fivelastproperty', methods: ['GET'])]
    public function fiveLastProperty(ArticlesRepository $articlesRepository)
    {
        $articles = $articlesRepository->fivelastproperty();

        return $this->renderForm('webapp/articles/edit.html.twig', [
            'articles' => $articles,
        ]);

    }

    #[Route('/onearticle/{id}', name: 'op_webapp_articles_onearticle', methods: ['GET'])]
    public function OneArticle(Articles $article): Response
    {
        return $this->render('webapp/articles/onearticle.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articlesByCat/{cat}', name: 'op_webapp_articles_articlesbycat', methods: ['GET'])]
    public function articlesByCat($cat, ArticlesRepository $articlesRepository): Response
    {
        //dd($cat);
        $articles = $articlesRepository->findBy(['category' => $cat], ['updatedAt'=> 'DESC']);
        //dd($articles);
        return $this->render('webapp/page/article/category.html.twig', [
            'articles' => $articles,
        ]);
    }
}
