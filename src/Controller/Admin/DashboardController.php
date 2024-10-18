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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $year = date("Y");

        $properties = $propertyRepository->StatsGraph($year);

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = isset($properties[$i-1]['month']);

            if($month == true){
                array_push($months, ['month' => $i, 'c_properties' => $properties[$i-1]['c_properties']] );
            }else{
                array_push($months, ['month' => $i, 'c_properties' => 0] );
            }
        }

        $months_label = array_column($months, 'month');
        $months_cproperties = array_column($months, 'c_properties');
        //dd($months_cproperties);

        $chart->setData([
            'labels' => $months_label,
            'datasets' => [
                [
                    'label' => 'Nombre de biens enregistrés sur l\'année '. $year,
                    'backgroundColor' => 'rgb(42, 86, 95)',
                    'borderColor' => 'rgb(rgb(42, 86, 95)',
                    'data' => $months_cproperties,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 15,
                ],
            ],
        ]);

        return $this->render('admin/dashboard/index.html.twig', [
            'chart' => $chart,
        ]);
    }

    // Personnalisation de la navbar
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

        if($timeless >= 600 ){
            $stTimeless = 3;
        }elseif ($timeless <= 599 && $timeless >= 300){
            $stTimeless = 2;
        }elseif ($timeless <= 299 && $timeless >= 50){
            $stTimeless = 1;
        }elseif($timeless <= 49){
            $stTimeless = 0;
        }

        return $this->json([
            'Code' => 200,
            'sttimeless' => $stTimeless
        ], 200);
    }

}
