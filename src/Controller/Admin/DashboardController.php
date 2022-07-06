<?php

namespace App\Controller\Admin;

use App\Repository\Admin\ApplicationRepository;
use App\Repository\Webapp\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/opadmin/dashboard', name: 'op_admin_dashboard_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }

    /**
     * Personnalisation de la navbar
     */
    #[Route("/webapp/public/menus", name:'op_webapp_public_listmenus')]
    public function NavBar(ApplicationRepository $applicationRepository,Request $request): Response
    {
        // on récupère l'utilisateur courant
        $user = $this->getUser();

        // préparation des éléments d'interactivité du menu
        $application = $applicationRepository->findFirstReccurence();

        return $this->render('include/admin/navbar_admin.html.twig', [
            'application' => $application,
        ]);
    }
}
