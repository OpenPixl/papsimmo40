<?php

namespace App\Controller\Webapp;

use App\Entity\Webapp\Articles;
use App\Form\Webapp\ArticlesType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Webapp\ArticlesRepository;
use App\Repository\Webapp\choice\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/webapp/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'op_webapp_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        $articles = $articlesRepository->listwithoutactuality();

        return $this->render('webapp/articles/index.html.twig', [
            'articles' => $articles,
            'page' => 'allArticles'
        ]);
    }

    #[Route('/actualites', name: 'op_webapp_articles_actualites', methods: ['GET'])]
    public function actualites(ArticlesRepository $articlesRepository): Response
    {
        $actualites = $articlesRepository->listbycategory();

        return $this->render('webapp/articles/actualites.html.twig', [
            'articles' => $actualites,
            'page' => 'actualities'
        ]);
    }

    #[Route('/new', name: 'op_webapp_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, ArticlesRepository $articlesRepository, EmployedRepository $employedRepository, CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        $actualite = $categoryRepository->find(1);

        $article = new Articles();
        $article->setAuthor($employed);
        $article->setCategory($actualite);
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_new'),
            'method' => 'POST',
            'attr' => [
                'id' => 'FormAddArticle'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // intégration du code du logo du client
            $articleFrontFile = $form->get('articleFrontFile')->getData();
            if ($articleFrontFile) {
                $originalFilename = pathinfo($articleFrontFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safearticleFrontFileName = $slugger->slug($originalFilename);
                $newarticleFrontFileName = $safearticleFrontFileName. '.' . $articleFrontFile->guessExtension();
                $pathdir = $this->getParameter('articles_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setArticleFrontName($newarticleFrontFileName);
                //$article->setLogoSize($logoFile->getSize());;
            }

            $articlesRepository->add($article);
            return $this->redirectToRoute('op_webapp_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webapp/articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/newactualite', name: 'op_webapp_articles_newactualite', methods: ['GET', 'POST'])]
    public function newActualite(Request $request,SluggerInterface $slugger, EntityManagerInterface $em, ArticlesRepository $articlesRepository, EmployedRepository $employedRepository, CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $actualite = $categoryRepository->find(2);

        //dd($actualite);

        $article = new Articles();
        $article->setAuthor($employed);
        $article->setCategory($actualite);
        //dd($article);
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_newactualite'),
            'method' => 'POST',
            'attr' => [
                'id' => 'FormAddArticle'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // intégration du code du logo du client
            $articleFrontFile = $form->get('articleFrontFile')->getData();
            if ($articleFrontFile) {
                $originalFilename = pathinfo($articleFrontFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safearticleFrontFileName = $slugger->slug($originalFilename);
                $newarticleFrontFileName = $safearticleFrontFileName. '.' . $articleFrontFile->guessExtension();
                $pathdir = $this->getParameter('articles_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setArticleFrontName($newarticleFrontFileName);
                //$article->setLogoSize($logoFile->getSize());;
            }

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('op_webapp_articles_actualites', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webapp/articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_webapp_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('webapp/page/article/showactualite.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/edit/{id}', name: 'op_webapp_articles_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Articles $article,
        ArticlesRepository $articlesRepository,
        SluggerInterface $slugger,
    ): Response
    {
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_edit', ['id'=> $article->getId()]),
            'method' => 'POST',
            'attr' => [
                'id' => 'FormEditArticle'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // intégration du code du logo du client
            $articleFrontFile = $form->get('articleFrontFile')->getData();
            if ($articleFrontFile) {
                $originalFilename = pathinfo($articleFrontFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safearticleFrontFileName = $slugger->slug($originalFilename);
                $newarticleFrontFileName = $safearticleFrontFileName. '.' . $articleFrontFile->guessExtension();
                $pathdir = $this->getParameter('articles_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $articleFrontFile->move(
                            $this->getParameter('articles_directory'),
                            $newarticleFrontFileName
                        );
                    }


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setArticleFrontName($newarticleFrontFileName);
                //$article->setLogoSize($logoFile->getSize());;
            }

            $articlesRepository->add($article);
            return $this->redirectToRoute('op_webapp_articles_edit', ['id'=>$article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webapp/articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/editactualite/{id}', name: 'op_webapp_articles_editactualite', methods: ['GET', 'POST'])]
    public function editactualite(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $form = $this->createForm(ArticlesType::class, $article, [
            'action' => $this->generateUrl('op_webapp_articles_editactualite', ['id'=> $article->getId()]),
            'method' => 'POST',
            'attr' => [
                'id' => 'FormEditArticle'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articlesRepository->add($article);
            return $this->redirectToRoute('op_webapp_articles_editactualite', ['id'=>$article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webapp/articles/edit.html.twig', [
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

    #[Route('/{id}/del/{page}', name: 'op_webapp_articles_del', methods: ['POST'])]
    public function del(Request $request, Articles $articles,ArticlesRepository $articlesRepository, $page)
    {
        $articlesRepository->remove($articles);
        if($page == 'allArticles'){
            $listarticles = $articlesRepository->listwithoutactuality();
        }elseif($page == 'actualities'){
            $listarticles = $articlesRepository->listbycategory();
        }

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'liste' => $this->renderView('webapp/articles/include/_liste.html.twig', [
                'articles' => $listarticles,
                'page' => $page
            ])
        ], 200);
    }

    #[Route('/fivelstproperty', name: 'op_webapp_articles_fivelastproperty', methods: ['GET'])]
    public function fiveLastProperty(ArticlesRepository $articlesRepository)
    {
        $articles = $articlesRepository->fivelastproperty();

        return $this->render('webapp/articles/edit.html.twig', [
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
        $articles = $articlesRepository->findBy(['category' => $cat, 'state' => 'publiée'], ['updatedAt'=> 'DESC'], 3);
        //dd($articles);
        return $this->render('webapp/page/article/category.html.twig', [
            'articles' => $articles,
        ]);
    }
}
