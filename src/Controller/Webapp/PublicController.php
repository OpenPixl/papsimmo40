<?php

namespace App\Controller\Webapp;

use App\Entity\Admin\Application;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Webapp\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends AbstractController
{
    #[Route('/webapp/public', name: 'op_webapp_public_homepage')]
    public function homepage(): Response
    {
        return $this->render('webapp/public/index.html.twig');
    }

    #[Route('/', name: 'op_webapp_public_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $parameter = $em->getRepository(Application::class)->findFirstReccurence();
        // boucle : verifie si le site est installé
        if(!$parameter){
            return $this->redirectToRoute('op_admin_dashboard_first_install');
        }
        else {
            $isOnline = $parameter->getIsOnline();
            if(!$isOnline) {
                return $this->redirectToRoute('op_webapp_public_offline');
            }
            else{
                return $this->redirectToRoute('op_webapp_public_homepage');
            }
        }
    }

    /**
     * Fonction d'affichage de la page hors ligne du site
     */
    #[route("/webapp/public/offline", name:'op_webapp_public_offline')]
    public function Offline(EntityManagerInterface $em) : Response
    {
        $parameter = $em->getRepository(Application::class)->findFirstReccurence();
        $sections = $em->getRepository(Section::class)->findBy(array('favorites' => 1));
        $isOnline = $parameter->getIsOnline();

        if ($isOnline == 1){
            return $this->render('webapp/public/index.html.twig', [
                'parameter' => $parameter,
                'sections' => $sections,
            ]);
        }
        return $this->render('webapp/public/offline.html.twig', [
            'parameter' => $parameter
        ]);
    }


    /**
     * @return Response
     * Fonction d'inclusion des balises "meta" pour le référencement
     */
    public function meta(EntityManagerInterface $em) : Response
    {
        $parameter = $em->getRepository(Application::class)->find(1);

        return $this->render('include/meta.html.twig', [
            'parameter' => $parameter
        ]);
    }

    /**
     * Affiche mles différents menus sur la page d'accueil
     */
    #[Route("/webapp/public/menus/{route}", name:'op_webapp_public_listmenus')]
    public function BlocMenu(PageRepository $pageRepository, ApplicationRepository $applicationRepository,Request $request, $route): Response
    {
        // on récupère l'utilisateur courant
        $user = $this->getUser();

        // préparation des éléments d'interactivité du menu
        $parameter = $applicationRepository->findFirstReccurence();
        $menus = $pageRepository->listMenu();

        return $this->render('include/public/navbar_webapp.html.twig', [
            'parameter' => $parameter,
            'menus' => $menus,
            'route' => $route
        ]);
    }


}
