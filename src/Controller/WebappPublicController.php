<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebappPublicController extends AbstractController
{
    #[Route('/webapp/public', name: 'app_webapp_public')]
    public function index(): Response
    {
        return $this->render('webapp_public/index.html.twig', [
            'controller_name' => 'WebappPublicController',
        ]);
    }
}
