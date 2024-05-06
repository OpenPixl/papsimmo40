<?php

namespace App\Controller\Soap;

use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProtexaService;


class protexaController extends AbstractController
{
    #[Route('/soap/protexa', name: 'op_admin_soap_protexa_index')]
    public function index(): Response
    {
        return $this->render('soap/protexa/index.html.twig', [
            'controller_name' => 'protexaController',
        ]);
    }

    #[Route('/soap/protexa/registre', name: 'op_admin_soap_protexa_registre')]
    public function registre(ProtexaService $protexaService): Response
    {
        $results = [];
        $parameters = [
            'Login_connexion' => 'testclient@protexa.fr',
            'MotdePasse'=> 'VHWHZF'
        ];

        $client = $protexaService->getClient();
        $wslisteresaencours = $protexaService->callService($client, 'wslisteresaencours', $parameters);

        //dd($listews['RESERVATION_EN_COURS']);

        foreach ($wslisteresaencours['RESERVATION_EN_COURS'] as $wsl){
            $parameters = [
                'Login' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $wsl['MANDAT']
            ];
            $wsdetailsmandat = $protexaService->callService($client, 'wsdetailsmandat', $parameters);
            array_push($results, [
                'ID' =>  $wsl['MANDAT'],
                'AGENCE' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE'],
                'DATE_DEBUT' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_DEBUT'],
                'DATE_FIN' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_FIN'],
                'TIER_NEGO' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['TIER_NEGO'],
                'NOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['NOM'],
                'DESCRIPTION_OBSERVATION' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS']['DESCRIPTION_OBSERVATION'],
            ]);
            //dd($wsdetailsmandat['MANDAT']['DONNEES']);
        }
        //dd($results);
        return $this->render('soap/protexa/registre.html.twig',[
            'results' => $results
        ]);
    }

    #[Route('/soap/protexa/listeresaencours', name: 'op_admin_soap_protexa_listeresaencours')]
    public function listeresaencours(): Response
    {
        $client = new SoapClient('https://production.protexa.fr/WSPROTEXA_WEB/awws/wsprotexa.awws?wsdl', ['trace' => 1]);

        $parameters = [
            'Login_connexion' => 'testclient@protexa.fr',
            'MotdePasse'=> 'VHWHZF'
        ];

        $results = (array)$client->__soapCall('wslisteresaencours', [
            'parameters' => $parameters
        ]);
        $xml = simplexml_load_string($results['wslisteresaencoursResult']);
        $json = json_encode($xml);
        $resas = json_decode($json,TRUE);

        return $this->render('registre.html.twig', [
            'resas' => $resas,
        ]);
    }

}
