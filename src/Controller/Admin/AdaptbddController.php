<?php

namespace App\Controller\Admin;

use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use App\Repository\Gestapp\ComplementRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function AdaptEntity(ComplementRepository $complementRepository, EntityManagerInterface $em)
    {
        $complements = $complementRepository->findAll();
        foreach($complements as $c){
            $propertyEnergy = $c->getPropertyEnergy();
            //$propertyEnergy = $em->getRepository(PropertyEnergy::class)->find($idpropertyEnergy);
            if($propertyEnergy){
                $c->addEnergy($propertyEnergy);
            }
            $em->flush();
        }

        return $this->json(['message' => 'Mise à jour BDD effectuée.'],200);
    }
}
