<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Cadaster;
use App\Form\Gestapp\CadasterType;
use App\Repository\Gestapp\CadasterRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/cadaster')]
class CadasterController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_cadaster_index', methods: ['GET'])]
    public function index(CadasterRepository $cadasterRepository): Response
    {
        return $this->render('gestapp/cadaster/index.html.twig', [
            'cadasters' => $cadasterRepository->findAll(),
        ]);
    }

    #[Route('/byproperty/{idProperty}', name: 'op_gestapp_cadaster_byproperty', methods: ['GET'])]
    public function ListByProperty(CadasterRepository $cadasterRepository, $idproperty, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->find($idproperty);
        // on récupère la liste de tous les fiches cadastres pour une proprieté précise
        $cadasters = $cadasterRepository->findBy(['property'=>$property]);

        return $this->render('gestapp/cadaster/listcadastersbyproperty.html.twig', [
            'cadasters' => $cadasters,
            'idproperty' => $idproperty
        ]);
    }

    #[Route('/new/{idproperty}', name: 'op_gestapp_cadaster_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CadasterRepository $cadasterRepository, $idproperty, PropertyRepository $propertyRepository): Response
    {
        $cadaster = new Cadaster();
        $property = $propertyRepository->find($idproperty);
        $form = $this->createForm(CadasterType::class, $cadaster, [
            'action' => $this->generateUrl('op_gestapp_cadaster_new', [
                'idproperty' => $idproperty
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cadaster->setProperty($property);
            $cadasterRepository->add($cadaster, true);

            $cadasters = $cadasterRepository->findBy(['property' => $property], ['id'=>'ASC']);
            $totalSurface = 0;
            foreach ($cadasters as $cad){
                $surface = $cad->getContenance();
                $totalSurface = $totalSurface + $surface;
            }

            return $this->json([
                'code'=> 200,
                'message' => "Les informations du cadastres ont été correctement ajoutées.",
                'm2' => $totalSurface,
                'listeCadaster' => $this->renderView('gestapp/cadaster/listcadastersbyproperty.html.twig', [
                    'cadasters' => $cadasters,
                    'idproperty' => $idproperty
                ])
            ], 200);
        }

        return $this->renderForm('gestapp/cadaster/new.html.twig', [
            'cadaster' => $cadaster,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_cadaster_show', methods: ['GET'])]
    public function show(Cadaster $cadaster): Response
    {
        return $this->render('gestapp/cadaster/show.html.twig', [
            'cadaster' => $cadaster,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_cadaster_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cadaster $cadaster, CadasterRepository $cadasterRepository): Response
    {
        $form = $this->createForm(CadasterType::class, $cadaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cadasterRepository->add($cadaster, true);

            return $this->redirectToRoute('op_gestapp_cadaster_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/cadaster/edit.html.twig', [
            'cadaster' => $cadaster,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_cadaster_delete', methods: ['POST'])]
    public function delete(Request $request, Cadaster $cadaster, CadasterRepository $cadasterRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cadaster->getId(), $request->request->get('_token'))) {
            $cadasterRepository->remove($cadaster, true);
        }

        return $this->redirectToRoute('op_gestapp_cadaster_index', [], Response::HTTP_SEE_OTHER);
    }

    /*
     * Suppression de la zone en json
     */
    #[Route('/delete/{id}/{idproperty}', name: 'op_gestapp_cadaster_deletejson', methods: ['POST'])]
    public function deleteJson(Cadaster $cadaster, CadasterRepository $cadasterRepository, $idproperty, PropertyRepository $propertyRepository)
    {
        $cadasterRepository->remove($cadaster, true);
        $property = $propertyRepository->find($idproperty);
        $cadasters = $cadasterRepository->findBy(['property' => $property], ['id'=>'ASC']);
        $totalSurface = 0;
        foreach ($cadasters as $cad){
            $surface = $cad->getContenance();
            $totalSurface = $totalSurface + $surface;
        }
        return $this->json([
            'code'=> 200,
            'message' => "La zone a été supprimée.",
            'm2' => $totalSurface,
            'listeCadaster' => $this->renderView('gestapp/cadaster/listcadastersbyproperty.html.twig', [
                'cadasters' => $cadasters,
                'idproperty' => $idproperty
            ])
        ], 200);
    }
}
