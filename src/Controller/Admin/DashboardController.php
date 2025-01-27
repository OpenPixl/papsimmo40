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
        $user = $this->getUser();
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        $this->denyAccessUnlessGranted('ROLE_EMPLOYED');
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $year = date("Y");

        if($hasAccess == 'true'){
            $properties = $propertyRepository->StatsGraph($year);
            $properties_old = $propertyRepository->StatsGraph($year-1);
        }else{
            $properties = $propertyRepository->StatsGraphUser($year,$user);
            $properties_old = $propertyRepository->StatsGraphUser($year-1, $user);
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = isset($properties[$i-1]['month']);

            $c_properties = isset($properties[$i-1]['c_properties']) ? $properties[$i-1]['c_properties'] : 0;
            $c_properties_old = isset($properties_old[$i-1]['c_properties']) ? $properties_old[$i-1]['c_properties'] : 0;
            //dd($c_properties);

            array_push($months, [
                'month' => $i,
                'c_properties' => $c_properties,
                'c_properties_old' => $c_properties_old
            ]);
        }

        $months_label = array_column($months, 'month');
        $months_cproperties = array_column($months, 'c_properties');
        $months_cpropertiesold = array_column($months, 'c_properties_old');
        //dd($months);

        $chart->setData([
            'labels' => $months_label,
            'datasets' => [
                [
                    'label' => 'Nombre de biens enregistrés en '. $year,
                    'backgroundColor' => 'rgb(40, 116, 166)',
                    'borderColor' => 'rgb(rgb(27, 79, 114)',
                    'data' => $months_cproperties,
                ],
                [
                    'label' => 'Nombre de biens enregistrés en '. $year-1,
                    'backgroundColor' => 'rgb(52, 152, 219)',
                    'borderColor' => 'rgb(rgb(40, 116, 166)',
                    'data' => $months_cpropertiesold,
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
