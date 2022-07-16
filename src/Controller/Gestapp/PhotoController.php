<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Photo;
use App\Form\Gestapp\PhotoType;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/photo')]
class PhotoController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('gestapp/photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/{idproperty}', name: 'op_gestapp_photo_byproperty', methods: ['GET'])]
    public function byProperty(PhotoRepository $photoRepository, PropertyRepository $propertyRepository, $idproperty): Response
    {
        $property = $propertyRepository->find($idproperty);
        //dd($idproperty);
        $photos = $photoRepository->findBy(['property'=>$property], ['id'=>'DESC']);
        //dd($photos);
        return $this->render('gestapp/photo/byproperty.html.twig', [
            'photos' => $photos,
            'idproperty' => $idproperty
        ]);
    }

    #[Route('/new/{idproperty}', name: 'op_gestapp_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PhotoRepository $photoRepository, $idproperty, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->find($idproperty);
        $photo = new Photo();
        $photo->setProperty($property);
        $form = $this->createForm(PhotoType::class, $photo, [
            'action' => $this->generateUrl('op_gestapp_photo_new', ['idproperty'=>$idproperty]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoRepository->add($photo);
            $photos = $photoRepository->findBy(['property'=>$property], ['id'=>'DESC']);
            return $this->json([
                'code'=> 200,
                'message' => "La photo du bien a été ajoutée",
                'listephoto' => $this->renderView('gestapp/photo/_listephoto.html.twig', [
                    'photos' => $photos,
                    'idproperty' => $idproperty
                ])
            ], 200);
        }

        return $this->renderForm('gestapp/photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'idproperty' => $idproperty
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('gestapp/photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoRepository->add($photo);
            return $this->json([
                'code'=> 200,
                'message' => "La photo du bien a été correctement modifiée."
            ], 200);
        }

        return $this->renderForm('gestapp/photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            $photoRepository->remove($photo);
        }

        return $this->redirectToRoute('app_gestapp_photo_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}/{idproperty}', name: 'app_gestapp_photo_del', methods: ['POST'])]
    public function del(Request $request, Photo $photo, PhotoRepository $photoRepository, PropertyRepository $propertyRepository, $idproperty): Response
    {
        $photoRepository->remove($photo);
        $property = $propertyRepository->find($idproperty);
        $photos = $photoRepository->findBy(['property'=>$property], ['id'=>'DESC']);
        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été supprimée",
            'listephoto' => $this->renderView('gestapp/photo/_listephoto.html.twig', [
                'photos' => $photos,
                'idproperty' => $idproperty
            ])
        ], 200);

    }
}
