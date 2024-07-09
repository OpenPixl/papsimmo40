<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\PublicationType;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use App\Service\ftptransfertService;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_publication_index', methods: ['GET'])]
    public function index(PublicationRepository $publicationRepository): Response
    {
        return $this->render('gestapp/publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PublicationRepository $publicationRepository): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_publication_show', methods: ['GET'])]
    public function show(Publication $publication): Response
    {
        return $this->render('gestapp/publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/showbyproperty/{id}', name: 'op_admin_contact_showbyproperty', methods: ['GET','POST'])]
    public function showByProperty(
        Request $request,
        Publication $publication,
        PublicationRepository $publicationRepository,
        PropertyRepository $propertyRepository,
        ftptransfertService $ftptransfertService,
        PhotoRepository $photoRepository,
        complementRepository $complementRepository
    ): Response
    {
        $form = $this->createForm(PublicationType::class, $publication,[
            'action' => $this->generateUrl('op_admin_contact_showbyproperty', ['id' => $publication->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            // mettre la propriété en fin de parcours création
            $property = $propertyRepository->findOneBy(['publication'=>$publication->getId()]);
            $property->setIsIncreating(0);
            $propertyRepository->add($property);
            // Service de dépot sur serveur le serveur FTP "SeLoger"
            $ftptransfertService->selogerFTP(
                $propertyRepository,
                $photoRepository,
                $complementRepository,
            );
            // Service de dépot sur serveur le serveur FTP "figaroImmo"
            $ftptransfertService->figaroFTP(
                $propertyRepository,
                $photoRepository,
                $complementRepository,
            );
            // Service de dépot sur serveur le serveur FTP "GreenAcres"
            $ftptransfertService->greenacresFTP(
                $propertyRepository,
                $photoRepository,
                $complementRepository
            );


            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/publication/showbyproperty.html.twig', [
            'publication' => $publication,
            'property' => $propertyRepository->findOneBy(['publication'=>$publication->getId()]),
            'form' => $form,
        ]);
    }

    public function publiconftp(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
        ftptransfertService $ftptransfertService,
    )
    {
        // Service de dépot sur serveur le serveur FTP "SeLoger"
        $ftptransfertService->selogerFTP(
            $propertyRepository,
            $photoRepository,
            $complementRepository,
        );
        // Service de dépot sur serveur le serveur FTP "figaroImmo"
        $ftptransfertService->figaroFTP(
            $propertyRepository,
            $photoRepository,
            $complementRepository,
        );
        // Service de dépot sur serveur le serveur FTP "figaroImmo"
        $ftptransfertService->greenacresFTP(
            $propertyRepository,
            $photoRepository,
            $complementRepository
        );
    }

    #[Route('/{id}/edit', name: 'app_gestapp_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    // Lister les biens par diffuseur
    #[Route('/listdiffuseur/{diffuseur}', name: 'app_gestapp_publication_seloger', methods: ['GET'])]
    public function listdiffuseur($diffuseur, PropertyRepository $propertyRepository, Request $request, PropertyService $propertyService, PhotoRepository $photoRepository, ComplementRepository $complementRepository)
    {

        if($diffuseur == 'SL'){
            $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger

            $rows = array();
            foreach ($properties as $property){
                $propriete = $propertyRepository->find($property['id']);
                //destination du bien
                $destination = $propertyService->getDestination($propriete);
                // Description de l'annonce
                $annonce = $propertyService->getAnnonce($propriete);
                //dd($annonce);

                $dates = $propertyService->getDates($property);

                // Calcul des honoraires en %
                //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
                //dd($property['price'], $property['priceFai'], $honoraires);

                // Récupération des images liées au bien
                $url = $propertyService->getUrlPhotos($property);
                $titrephoto = $propertyService->getTitrePhotos($property);

                // Orientation
                if($property['orientation'] = 'nord'){
                    $nord = 1;
                    $est = 0;
                    $sud = 0;
                    $ouest = 0;
                }elseif($property['orientation'] = 'est'){
                    $nord = 0;
                    $est = 1;
                    $sud = 0;
                    $ouest = 0;
                }elseif($property['orientation'] = 'sud'){
                    $nord = 0;
                    $est = 0;
                    $sud = 1;
                    $ouest = 0;
                }else{
                    $nord = 0;
                    $est = 0;
                    $sud = 0;
                    $ouest = 1;
                }

                // publication sur les réseaux
                $publications = 'SL';
                // version du document
                $version = '4.11';

                // Transformation terrace en booléen
                if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

                $infos = ['refDossier' => 'RC1860977', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

                // Equipements
                $idcomplement = $property['idComplement'];
                $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
                //dd($equipments);

                // Récupération DPE & GES
                $bilanDpe = $propertyService->getClasseDpe($propriete);
                $bilanGes = $propertyService->getClasseGes($propriete);

                // Création d'une ligne du tableau
                $data = $propertyService->arrayRowSLFIG($propriete, $destination, $dates, $infos, $url, $titrephoto, $property);
                $rows[] = implode('!#', $data);
            }
            $content = implode("\n", $rows);
            dd($content);
        }
        elseif ($diffuseur == 'FIG'){
            $properties = $propertyRepository->reportpropertyfigaroFTP();

            $rows = array();
            foreach ($properties as $property){
                $propriete = $propertyRepository->find($property['id']);
                //destination du bien
                $destination = $propertyService->getDestination($propriete);
                // Description de l'annonce
                $annonce = $propertyService->getAnnonce($propriete);
                //dd($annonce);

                $dates = $propertyService->getDates($property);

                // Calcul des honoraires en %
                //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
                //dd($property['price'], $property['priceFai'], $honoraires);

                // Récupération des images liées au bien
                $url = $propertyService->getUrlPhotos($property);
                $titrephoto = $propertyService->getTitrePhotos($property);

                // Orientation
                if($property['orientation'] = 'nord'){
                    $nord = 1;
                    $est = 0;
                    $sud = 0;
                    $ouest = 0;
                }elseif($property['orientation'] = 'est'){
                    $nord = 0;
                    $est = 1;
                    $sud = 0;
                    $ouest = 0;
                }elseif($property['orientation'] = 'sud'){
                    $nord = 0;
                    $est = 0;
                    $sud = 1;
                    $ouest = 0;
                }else{
                    $nord = 0;
                    $est = 0;
                    $sud = 0;
                    $ouest = 1;
                }

                // publication sur les réseaux
                $publications = 'Figaro';
                // version du document
                $version = '4.11';
                // Transformation terrace en booléen
                if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

                $infos = ['refDossier' => '107428', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

                // Equipements
                $idcomplement = $property['idComplement'];
                $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
                //dd($equipments);

                // Récupération DPE & GES
                $bilanDpe = $propertyService->getClasseDpe($propriete);
                $bilanGes = $propertyService->getClasseGes($propriete);

                // Création d'une ligne du tableau
                $data = $propertyService->arrayRowSLFIG($propriete, $destination, $dates, $infos, $url, $titrephoto, $property);
                $rows[] = implode('!#', $data);
            }
            $content = implode("\n", $rows);
            dd($content);
        }
        else{
            $properties = [];
            dd($properties);
        }
    }

    #[Route('/{id}', name: 'app_gestapp_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            $publicationRepository->remove($publication);
        }

        return $this->redirectToRoute('app_gestapp_publication_index', [], Response::HTTP_SEE_OTHER);
    }
}
