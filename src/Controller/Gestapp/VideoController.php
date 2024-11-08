<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Photo;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Video;
use App\Form\Gestapp\VideoType;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/video')]
class VideoController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_video_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('gestapp/video/index.html.twig', [
            'videos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/new/{idproperty}', name: 'app_gestapp_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $idproperty, SluggerInterface $slugger,): Response
    {
        //dd('id property :' .$idproperty);

        $property = $entityManager->getRepository(Property::class)->find($idproperty);
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video, [
            'action' => $this->generateUrl('app_gestapp_video_new', ['idproperty' => $idproperty]),
            'method' => 'POST',
            'attr' => [
                'id' => 'addVideo'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoFile = $form->get('videoFile')->getData();

            if($videoFile){
                $originalvideoName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safevideoName = $slugger->slug($originalvideoName);
                $newvideoName = $safevideoName . '.' . $videoFile->guessExtension();

                // Préparation pour déplacer la vidéo vers le dossier de la propriété
                $ref = explode("/", $property->getRef());
                $newref = $ref[0].'-'.$ref[1];
                $pathdir = $this->getParameter('property_photo_directory')."/".$newref."/";
                try {
                    if (is_dir($pathdir)){
                        $videoFile->move(
                            $pathdir,
                            $newvideoName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $videoFile->move(
                            $pathdir,
                            $newvideoName
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $video->setPath($newref);
                $video->setVideoName($newvideoName);
            }

            $property->setVideo($video);

            $entityManager->persist($video);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'La vidéo à correctement été déposée sur le serveur',
            ], 200);
        }

        return $this->render('gestapp/video/new.html.twig', [
            'property' => $property,
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        return $this->render('gestapp/video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestapp_video_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/del', name: 'op_gestapp_video_del', methods: ['POST'])]
    public function del(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        dd($video);
        $nameVideo = $video->getVideoName();
        $repVideo = $video->getPath();
        $path = $this->getParameter('property_photo_directory')."/".$repVideo."/".$nameVideo;

        if (file_exists($path)){
            unlink($path);
        }else{
            return $this->json(['code' => 300,'message' => 'Le fichier n\'existe plus dans le serveur.']);
        }

        $video->getProperty()->setVideo(null);
        $entityManager->remove($video);
        $entityManager->flush();

        return $this->json(['code' => 200]);
    }
}
