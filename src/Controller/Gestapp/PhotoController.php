<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Photo;
use App\Form\Gestapp\PhotoType;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use http\Header;
use http\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        $photos = $photoRepository->findBy(['property'=>$property], ['position'=>'ASC']);
        //dd($photos);
        return $this->render('gestapp/photo/byproperty.html.twig', [
            'photos' => $photos,
            'property' => $property
        ]);
    }

    #[Route('/public/{idproperty}', name: 'op_gestapp_photo_bypropertypublic', methods: ['GET'])]
    public function byPropertyPublic(PhotoRepository $photoRepository, PropertyRepository $propertyRepository, $idproperty, Request $request): Response
    {
        $photo = $photoRepository->FirstPhoto($idproperty);
        if(!$photo){
            return $this->render('gestapp/photo/bypropertypublicnull.html.twig');
        }
        return $this->render('gestapp/photo/bypropertypublic.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/list/{idproperty}', name: 'op_gestapp_photo_includeinlistproperty', methods: ['GET'])]
    public function includeinlistproperty(PhotoRepository $photoRepository, PropertyRepository $propertyRepository, $idproperty, Request $request): Response
    {
        $photo = $photoRepository->FirstPhoto($idproperty);
        if(!$photo){
            return $this->render('gestapp/photo/includeinlistpropertynull.html.twig');
        }
        return $this->render('gestapp/photo/includeinlistproperty.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/new/{idproperty}', name: 'op_gestapp_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PhotoRepository $photoRepository, $idproperty, PropertyRepository $propertyRepository, SluggerInterface $slugger): Response
    {
        $property = $propertyRepository->find($idproperty);
        // on récupére si elle existe la dernière photo du bien actuel et son positionnement
        $lastphoto = $photoRepository->Lastphoto($idproperty);

        // récupération de la référence
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];
        $photo = new Photo();
        if($lastphoto){
            $position = $lastphoto->getPosition() + 1;
            $photo->setPosition($position);
        }else{
            $photo->setPosition(1);
        }

        $photo->setProperty($property);

        $form = $this->createForm(PhotoType::class, $photo, [
            'action' => $this->generateUrl('op_gestapp_photo_new', ['idproperty'=>$idproperty]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // upload de photo
            $photoFile = $form->get('galeryFrontFile')->getData();
            if ($photoFile) {
                $originalphotoFileName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safephotoFileName = $slugger->slug($originalphotoFileName);
                $newphotoFileName = $safephotoFileName . '-' . uniqid() . '.' . $photoFile->guessExtension();
                $pathdir = $this->getParameter('property_photo_directory')."/".$newref."/";
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $photoFile->move(
                            $pathdir,
                            $newphotoFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0770, true);
                        // Déplacement de la photo
                        $photoFile->move(
                            $pathdir,
                            $newphotoFileName
                        );
                    }

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $photo->setPath($newref);
                $photo->setGaleryFrontName($newphotoFileName);
            }

            $photoRepository->add($photo);
            $photos = $photoRepository->findBy(['property'=>$property], ['position'=>'ASC']);
            return $this->json([
                'code'=> 200,
                'message' => "La photo du bien a été ajoutée",
                'listephoto' => $this->renderView('gestapp/photo/_listephoto.html.twig', [
                    'photos' => $photos,
                    'property' => $property
                ])
            ], 200);
        }

        return $this->renderForm('gestapp/photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'idproperty' => $idproperty
        ]);
    }

    #[Route('/show/{id}', name: 'op_gestapp_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('gestapp/photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, PhotoRepository $photoRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('galeryFrontFile')->getData();
            if ($photoFile) {
                //suppression de l'image si elle est présente.
                $galeryFrontName = $photo->getGaleryFrontName();
                if($galeryFrontName){
                    $pathname = $this->getParameter('property_photo_directory').'/'.$galeryFrontName;
                    if(file_exists($pathname)){
                        unlink($pathname);
                    }
                }
                // Ajout de la nouvelle photo
                $originalphotoFileName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safephotoFileName = $slugger->slug($originalphotoFileName);
                $newphotoFileName = $safephotoFileName . '-' . uniqid() . '.' . $photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('property_photo_directory'),
                        $newphotoFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $photo->setGaleryFrontName($newphotoFileName);
            }

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

    #[Route('/{id}', name: 'op_gestapp_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            $photoRepository->remove($photo);
        }

        return $this->redirectToRoute('op_gestapp_photo_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}/{idproperty}', name: 'op_gestapp_photo_del', methods: ['POST'])]
    public function del(Request $request, Photo $photo, PhotoRepository $photoRepository, PropertyRepository $propertyRepository, $idproperty): Response
    {
        //suppression de l'image si elle est présente.
        $galeryFrontName = $photo->getGaleryFrontName();
        $ref = $photo->getPath();
        if($galeryFrontName){
            $pathname = $this->getParameter('property_photo_directory').'/'.$ref.'/'.$galeryFrontName;
            if(file_exists($pathname)){
                unlink($pathname);
            }
        }
        $photoRepository->remove($photo);
        $property = $propertyRepository->find($idproperty);
        $photos = $photoRepository->findBy(['property'=>$property], ['position'=>'ASC']);
        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été supprimée",
            'listephoto' => $this->renderView('gestapp/photo/_listephoto.html.twig', [
                'photos' => $photos,
                'property' => $property
            ])
        ], 200);

    }

    #[Route('/publicgallerybyproperty/{idproperty}', name: 'op_gestapp_photo_publicgallerybyproperty', methods: ['POST'])]
    public function PublicGalleryByProperty($idproperty, PhotoRepository $photoRepository)
    {
        $photos = $photoRepository->findBy(['property'=>$idproperty], ['position' => 'ASC']);
        //dd($photos);
        return $this->render('webapp/page/property/include/galerie.html.twig',[
            'photos' => $photos
        ]);
    }

    #[Route('/updatepositionphoto/{idcol}/{key}', name: 'op_gestapp_photo_updatepositionphoto', methods: ['POST'])]
    public function updatepositionphoto($idcol, PhotoRepository $photoRepository, $key)
    {
        // récupérer la photo correspondant à l'id
        $photo = $photoRepository->find($idcol);
        // mettre à jour le positionnnement
        $photo->setPosition($key+1);
        // mettre à jour la bdd
        $photoRepository->add($photo);

        return $this->json([
            'code'=> 200,
            'message' => "La photo a bien été déplacée."
            ], 200);
    }


}
