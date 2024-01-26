<?php

namespace App\Controller\Webapp;

use App\Entity\Webapp\Section;
use App\Form\Webapp\SectionbypageType;
use App\Form\Webapp\SectionType;
use App\Repository\Webapp\PageRepository;
use App\Repository\Webapp\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webapp/section')]
class SectionController extends AbstractController
{
    #[Route('/', name: 'app_webapp_section_index', methods: ['GET'])]
    public function index(SectionRepository $sectionRepository): Response
    {
        return $this->render('webapp/section/index.html.twig', [
            'sections' => $sectionRepository->findAll(),
        ]);
    }

    #[Route('/{idpage}', name: 'app_webapp_section_bypage', methods: ['GET'])]
    public function bypage($idpage, SectionRepository $sectionRepository, PageRepository $pageRepository): Response
    {
        return $this->render('webapp/section/bypage.html.twig', [
            'sections' => $sectionRepository->findBy(['page'=>$idpage],['position'=>'ASC']),
            'page' => $pageRepository->find($idpage)
        ]);
    }

    #[Route('/new/{idpage}', name: 'op_webapp_section_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SectionRepository $sectionRepository, $idpage, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->find($idpage);
        //dd($idpage);
        $user = $this->getUser();

        $section = new Section();
        $section->setPage($page);
        $section->setAuthor($user);
        $form = $this->createForm(SectionType::class, $section, [
            'action' => $this->generateUrl('op_webapp_section_new', ['idpage'=>$idpage]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dd($section);
            $sectionRepository->add($section);
            return $this->redirectToRoute('op_webapp_page_edit', [
                'id' => $idpage
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('webapp/section/new.html.twig', [
            'section' => $section,
            'form' => $form,
        ]);
    }


    #[Route('/newpage/{idpage}', name: 'app_webapp_section_newpage', methods: ['GET', 'POST'])]
    public function newpage($idpage, Request $request, SectionRepository $sectionRepository, PageRepository $pageRepository)
    {
        //dd($request);

        // Partie préparation
        // récupération des dernières information de positionnement des sections sur la page
        $page = $pageRepository->find($idpage);
        $user = $this->getUser();

        // Partie incrémentation dans la base de données
        $section = new Section();

        $form = $this->createForm(SectionbypageType::class, $section, [
            'action' => $this->generateUrl('app_webapp_section_newpage', ['idpage' => $idpage]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $section->setPage($page);
            $section->setAuthor($user);
            $sectionRepository->add($section);

            return $this->redirectToRoute('op_webapp_page_edit', [
                'id' => $idpage
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('webapp/section/new.html.twig', [
            'section' => $section,
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/newjson/{idpage}', name: 'op_webapp_section_newjson', methods: ['POST'])]
    public function addjson($idpage, Request $request, SectionRepository $sectionRepository, PageRepository $pageRepository)
    {
        $datanew = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $page = $pageRepository->find($idpage);

        $section = new Section();
        $section->setName($datanew['sectionbypage_name']);
        $section->setPage($page);
        $section->setAuthor($user->getId());
        $section->setIsSectionfluid($datanew['sectionbypage_isSectionfluid']);
        $section->setIsShowdate($datanew['sectionbypage_isShowdate']);
        $section->setIsShowtitle($datanew['sectionbypage_isShowtitle']);
        $section->setIsShowdescription($datanew['sectionbypage_isShowdescription']);

        $sections = $sectionRepository->findBy(['page'=> $page->getId()]);

        return $this->json([
            'code'      => 200,
            'message'   => "Ok",
            'liste' => $this->renderView('webapp/section/_liste.html.twig', [
                'sections' => $sections,
                'page' => $page,
            ])
        ], 200);

    }

    #[Route('/{id}', name: 'app_webapp_section_show', methods: ['GET'])]
    public function show(Section $section): Response
    {
        return $this->render('webapp/section/show.html.twig', [
            'section' => $section,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_webapp_section_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        $page = $section->getPage();
        $idpage = $page->getId();

        $form = $this->createForm(SectionType::class, $section, [
            'action' => $this->generateUrl('op_webapp_section_edit', ['id' => $section->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sectionRepository->add($section);
            return $this->redirectToRoute('op_webapp_page_edit', ['id'=> $idpage], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('webapp/section/edit.html.twig', [
            'section' => $section,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_webapp_section_delete', methods: ['POST'])]
    public function delete(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
            $sectionRepository->remove($section);
        }

        return $this->redirectToRoute('app_webapp_section_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}', name: 'op_webapp_section_del', methods: ['POST'])]
    public function del(Request $request, Section $section, SectionRepository $sectionRepository)
    {
        $idpage = $section->getPage();
        $sectionRepository->remove($section);

        $listsection = $sectionRepository->findBy(['page'=>$idpage]);

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'liste' => $this->renderView('webapp/section/_liste.html.twig', [
                'sections' => $listsection
            ])
        ], 200);
    }

    #[Route('/frontbypage/{idpage}', name: 'op_webapp_section_frontbypage', methods: ['POST'])]
    public function frontByPage(SectionRepository $sectionRepository, $idpage): Response
    {
        $sections = $sectionRepository->findBy(array('page' => $idpage), array('position' => 'ASC'));
        return $this->render('webapp/section/frontbypage.html.twig', [
            'sections' => $sections,
        ]);
    }
}
