<?php

namespace App\Controller\Gestapp;

use App\Repository\Gestapp\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeocodeController extends AbstractController
{
    #[Route('/gestapp/geocode', name: 'app_gestapp_geocode')]
    public function index(): Response
    {
        return $this->render('gestapp/geocode/index.html.twig', [
            'controller_name' => 'GeocodeController',
        ]);
    }

    #[Route('/gestapp/addpropertycoords/{idproperty}', name: 'op_gestapp_geocode_addpropertycoords', methods: ['POST'])]
    public function addPropertyCoords($idproperty, Request $request, PropertyRepository $propertyRepository, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $property = $propertyRepository->find($idproperty);
        $property->setCoordLat($data[0]);
        $property->setCoordLong($data[1]);
        $em->persist($property);
        $em->flush();

        return $this->json([
            "code" => 200,
            "message" => 'Modification apportée.'
        ],200);
    }
}
