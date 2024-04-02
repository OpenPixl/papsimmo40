<?php

namespace App\Controller\Admin;

use App\Repository\Admin\ApplicationRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Webapp\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SessionService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/opadmin/dashboard', name: 'op_admin_dashboard_index')]
    public function index(Request $request, SessionService $sessionService, ChartBuilderInterface $chartBuilder, PropertyRepository $propertyRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        //$property = $propertyRepository->StatsGraph();

        //dd($property);

        $chart->setData([
            'labels' => ['Janv.', 'Fev.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            'datasets' => [
                [
                    'label' => 'Nombre de biens enregistrés',
                    'backgroundColor' => 'rgb(42, 86, 95)',
                    'borderColor' => 'rgb(rgb(42, 86, 95)',
                    'data' => [0, 9, 5, 2, 13, 7, 15],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 20,
                ],
            ],
        ]);

        return $this->render('admin/dashboard/index.html.twig', [
            'chart' => $chart,
        ]);
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

    #[Route('/opadmin/dashboard/sessionstatut', name: 'op_admin_dashboard_sessionstatut')]
    public function sessionStatut(SessionService $sessionService)
    {
        $timeless = $sessionService->Timeless();

        return $this->json([
            'Code' => 200,
            'timeless' => $timeless
        ], 200);
    }

}
