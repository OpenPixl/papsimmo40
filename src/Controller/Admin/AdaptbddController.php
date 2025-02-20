<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Repository\Admin\EmployedRepository;
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
    public function AdaptEntity(EmployedRepository $employedRepository, EntityManagerInterface $em)
    {
        $complements = $employedRepository->findAll();
        foreach($complements as $c){
            $name = $c->getFirstName();
            $c->setFirstName($name);
            $em->flush();
        }

        return $this->json(['message' => 'Mise à jour BDD effectuée.'],200);
    }
}
