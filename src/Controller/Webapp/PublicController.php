<?php

namespace App\Controller\Webapp;

use App\Entity\Admin\Application;
use App\Form\SearchPropertyHomeType;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Webapp\PageRepository;
use App\Repository\Webapp\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends AbstractController
{
    #[Route('/home', name: 'op_webapp_public_homepage')]
    public function homepage(SectionRepository $sectionRepository, ApplicationRepository $applicationRepository): Response
    {
        $sections = $sectionRepository->findBy(['isfavorite' => 1]);
        $application = $applicationRepository->findFirstReccurence();

        if (!$application) {
            return $this->redirectToRoute('op_admin_dashboard_first_install');
        } else {
            $isOnline = $application->getIsOnline();
            if ($isOnline == 1) {
                return $this->redirectToRoute('op_webapp_public_offline');
            } else {
                return $this->render('webapp/public/index.html.twig', [
                    'application' => $application,
                    'sections' => $sections
                ]);
            }
        }
    }

    #[Route('/', name: 'op_webapp_public_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->findFirstReccurence();
        // boucle : verifie si le site est installé
        //dd($application);
        if (!$application) {
            return $this->redirectToRoute('op_admin_dashboard_first_install');
        } else {
            $isOnline = $application->getIsOnline();
            //dd($isOnline);
            if ($isOnline == 0) {
                return $this->redirectToRoute('op_webapp_public_homepage');
            } else {
                return $this->redirectToRoute('op_webapp_public_offline');
            }
        }
    }

    // Fonction d'affichage de la page hors ligne du site
    #[route("/webapp/public/offline", name: 'op_webapp_public_offline')]
    public function Offline(EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->findFirstReccurence();

        if (!$application) {
            return $this->redirectToRoute('op_admin_dashboard_first_install');
        } else {
            $isOnline = $application->getIsOnline();

            if ($isOnline == 1) {
                return $this->render('webapp/public/offline.html.twig', [
                    'application' => $application
                ]);
            } else {
                return $this->redirectToRoute('op_webapp_public_homepage');
            }
        }
    }


    // Fonction d'inclusion des balises "meta" pour le référencement
    public function meta(EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find(1);

        return $this->render('include/meta.html.twig', [
            'application' => $application
        ]);
    }

    // Affiche les différents menus sur la page d'accueil
    #[Route("/webapp/public/menus/{route}", name: 'op_webapp_public_listmenus')]
    public function BlocMenu(PageRepository $pageRepository, ApplicationRepository $applicationRepository, PropertyRepository $propertyRepository, Request $request, $route): Response
    {
        // on récupère l'utilisateur courant
        $user = $this->getUser();

        // préparation des éléments d'interactivité du menu
        $application = $applicationRepository->findFirstReccurence();
        $menus = $pageRepository->listMenu();

        return $this->render('include/public/navbar_webapp.html.twig', [
            'application' => $application,
            'menus' => $menus,
            'route' => $route,
        ]);
    }

    // Affiche les différents menus sur la page d'accueil
    #[Route("/webapp/public/jumbotron/{route}", name: 'op_webapp_public_jumbotron')]
    public function Jumbotron(PageRepository $pageRepository, ApplicationRepository $applicationRepository, PropertyRepository $propertyRepository, Request $request, $route): Response
    {
        // on récupère l'utilisateur courant
        $user = $this->getUser();

        // préparation des éléments d'interactivité du menu
        $application = $applicationRepository->findFirstReccurence();

        return $this->render('include/public/jumbotron.html.twig', [
            'application' => $application,
            'route' => $route,
        ]);
    }

    // Affiche le bottom footer
    #[Route("/webapp/public/footer/top", name: 'op_webapp_public_topfooter')]
    public function topFooter(PageRepository $pageRepository, ApplicationRepository $applicationRepository, Request $request): Response
    {
        // préparation des éléments d'interactivité du menu
        $application = $applicationRepository->findFirstReccurence();

        $pages = $pageRepository->findBy(['state'=> 'publiée']);

        return $this->render('include/public/topfooter.html.twig', [
            'application' => $application,
            'pages' => $pages
        ]);
    }

    //Affiche le bottom footer
    #[Route("/webapp/public/footer/bottom", name: 'op_webapp_public_bottomfooter')]
    public function bottomFooter(ApplicationRepository $applicationRepository, Request $request): Response
    {

        // préparation des éléments d'interactivité du menu
        $application = $applicationRepository->findFirstReccurence();

        return $this->render('include/public/bottomfooter.html.twig', [
            'application' => $application,
        ]);
    }
}
