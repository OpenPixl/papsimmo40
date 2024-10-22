<?php

namespace App\Controller\Admin;

use App\Entity\Gestapp\Complement;
use App\Repository\Gestapp\ComplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdaptbddController extends AbstractController
{
    #[Route('/admin/adaptbdd', name: 'app_admin_adaptbdd_index')]
    public function index(): Response
    {
        return $this->render('admin/adaptbdd/index.html.twig', [
            'controller_name' => 'AdaptbddController',
        ]);
    }

    #[Route('/admin/adaptbdd/entity', name: 'app_admin_adaptbdd_entity')]
    public function AdaptEntity(ComplementRepository $complementRepository)
    {
        $complements = $complementRepository->findAll();
        //dd($complements);
        foreach($complements as $c){

            $propertyEnergy = $c->getPropertyEnergy();
            //dd($propertyEnergy);
            $c->addEnergy($propertyEnergy);
        }

        return $this->json(200, ['message' => 'Mise à jour BDD éffectuée.']);
    }
}
