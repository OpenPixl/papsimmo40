<?php

namespace App\Controller\Soap;

use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class protexaController extends AbstractController
{
    #[Route('/soap/protexa', name: 'op_admin_soap_protexa_index')]
    public function index(): Response
    {
        return $this->render('soap/protexa/index.html.twig', [
            'controller_name' => 'protexaController',
        ]);
    }

    #[Route('/soap/protexa/listeresaencours', name: 'op_admin_soap_protexa_listeresaencours')]
    public function listeresaencours(): Response
    {
        $client = new SoapClient('https://production.protexa.fr/WSPROTEXA_WEB/awws/wsprotexa.awws');

        dd($client);

        return $this->render('soap/protexa/index.html.twig', [
            'controller_name' => 'protexaController',
        ]);
    }

}
