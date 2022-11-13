<?php

namespace App\Controller\Webapp;

use App\Entity\Webapp\Page;
use App\Form\SearchPropertyHomeType;
use App\Form\Webapp\PageType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Webapp\PageRepository;
use App\Repository\Webapp\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/webapp/page')]
class PageController extends AbstractController
{
    #[Route('/', name: 'op_webapp_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('webapp/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_webapp_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PageRepository $pageRepository, EmployedRepository $employedRepository): Response
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $page = new Page();
        $page->setAuthor($employed);
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pageRepository->add($page);
            return $this->redirectToRoute('op_webapp_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('webapp/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_webapp_page_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('webapp/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_webapp_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pageRepository->add($page);
            return $this->redirectToRoute('op_webapp_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('webapp/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_webapp_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $pageRepository->remove($page);
        }

        return $this->redirectToRoute('op_webapp_page_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * affiche la page en front office selon le slug
     */
    #[Route('/slug/{slug}', name:'op_webapp_page_slug' , methods: ["GET"])]
    public function slug($slug, PageRepository $pageRepository, SectionRepository $sectionRepository) : response
    {
        $page = $pageRepository->findbyslug($slug);
        $sections = $sectionRepository->findByPageSlug($slug);

        return $this->render('webapp/public/index.html.twig', [
            'page' => $page,
            'sections' => $sections
        ]);
    }

    /**
     * Création du formulaire de recherche de biens depuis la page d'accueil
     */
    #[Route('/search/propertyhome', name:'op_webapp_page_searchpropertyhome' , methods: ["POST", "GET"])]
    public function formSearchPropertyHome(PropertyRepository $propertyRepository, Request $request) : response
    {
        // mise en place du formulaire de recherche dans le Jumbotron
        $form = $this->createForm(SearchPropertyHomeType::class, [
            'action' => $this->generateUrl('op_webapp_page_searchpropertyhome'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // on recherche les propriétés correspondantes
            $properties = $propertyRepository->SearchPropertyHome($form->get('keys')->getData());

            //dd($properties);

            return $this->render('webapp/page/property/searchpropertyhome.html.twig',[
                'properties' => $properties
            ]);
        }

        return $this->renderForm('webapp/page/property/include/formSearchpropertyhome.html.twig', [
            'form' => $form,
        ]);

    }

}
