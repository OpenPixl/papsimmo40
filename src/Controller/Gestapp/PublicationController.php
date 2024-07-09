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

        return $this->renderForm('gestapp/publication/new.html.twig', [
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
            // Service de dépot sur serveur le serveur FTP "figaroImmo"
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

        return $this->renderForm('gestapp/publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    // Lister les biens par diffuseur
    #[Route('/listdiffuseur/{diffuseur}', name: 'app_gestapp_publication_seloger', methods: ['GET'])]
    public function listdiffuseur($diffuseur, PropertyRepository $propertyRepository, Request $request, PropertyService $propertyService, PhotoRepository $photoRepository, ComplementRepository $complementRepository)
    {

        if($diffuseur == 'SL'){
            $properties = $propertyRepository->reportpropertycsv3();
            //$request = $this->requestStack->getCurrentRequest();
            $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger

            // Création de l'url pour les photos
            $fullHttp = $request->getUri();
            $scheme = parse_url($fullHttp, PHP_URL_SCHEME);
            $port = parse_url($fullHttp, PHP_URL_PORT);
            $host = parse_url($fullHttp, PHP_URL_HOST);
            //dd($scheme, $port, $host);
            if (!$port){
                $app = $scheme.'://'.$host;
            }else{
                $app = $scheme.'://'.$host.':'.$port;
            }

            $rows = array();
            foreach ($properties as $property){
                $propriete = $propertyRepository->find($property['id']);
                //destination du bien
                $destination = $this->propertyService->getDestination($propriete);
                // Description de l'annonce
                $annonce = $propertyService->getAnnonce($property);
                //dd($annonce);

                // Récupération de la reference
                $refMandat = $property['refMandat'];

                // Sélection du type de bien
                $bien = $propertyService->getBien($property);

                // Préparation de la date dpeAt
                if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                    $dpeAt = $property['dpeAt']->format('d/m/Y');
                }else{
                    $dpeAt ="";
                }

                // Préparation de la date de réation mandat
                if ($property['mandatAt'] && $property['mandatAt'] instanceof \DateTime) {
                    $mandatAt = $property['mandatAt']->format('d/m/Y');
                }else{
                    $mandatAt ="";
                }

                // Préparation de la date de création RefDPE
                if ($property['RefDPE'] && $property['RefDPE'] instanceof \DateTime) {
                    $RefDPE = $property['RefDPE']->format('d/m/Y');
                }else{
                    $RefDPE ="";
                }

                // Préparation de la date de disponibilité
                if ($property['disponibilityAt'] instanceof \DateTime) {
                    $disponibilityAt = $property['disponibilityAt']->format('d/m/Y');
                }else{
                    $disponibilityAt ="";
                }

                // Calcul des honoraires en %
                //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
                //dd($property['price'], $property['priceFai'], $honoraires);

                // Récupération des images liées au bien
                $photos = $photoRepository->findNameBy(['property' => $property['id']]);
                if(!$photos){                                                                       // Si aucune photo présente
                    $url = [];
                    $titrephoto = [];
                    for ($i = 1; $i<31; $i++){
                        ${'url'.$i} = '';
                        array_push($url, ${'url'.$i});
                    }
                    // génération des titres de photos
                    for ($i = 1; $i<31; $i++){
                        ${'titrephoto'.$i} = '';
                        array_push($titrephoto, ${'titrephoto'.$i});
                    }
                }else{
                    $url = [];
                    $arraykey = array_keys($photos);
                    for ($key = 0; $key<30; $key++){
                        if(array_key_exists($key,$arraykey)){
                            ${'url'.$key+1} = $app.'/properties/'.$photos[$key]['path'].'/'.$photos[$key]['galeryFrontName']."?".$photos[$key]['createdAt']->format('Ymd');
                            array_push($url, ${'url'.$key+1});
                        }else{
                            ${'url'.$key+1} = '';
                            array_push($url, ${'url'.$key+1});
                        }
                    }
                    // génération des titres de photos
                    for ($key = 0; $key<30; $key++){
                        if(array_key_exists($key,$arraykey)){
                            ${'titrephoto'.$key+1} = 'Photo-'.$property['ref'].'-'.$key+1;
                            array_push($url, ${'titrephoto'.$key+1});
                        }else{
                            ${'titrephoto'.$key+1} = '';
                            array_push($url, ${'titrephoto'.$key+1});
                        }
                    }
                }

                // Orientation
                $orientation = $property['orientation'];
                if($orientation = 'nord'){
                    $nord = 1;
                    $est = 0;
                    $sud = 0;
                    $ouest = 0;
                }elseif($orientation = 'est'){
                    $nord = 0;
                    $est = 1;
                    $sud = 0;
                    $ouest = 0;
                }elseif($orientation = 'sud'){
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

                // Transformation terrace en booléen
                if($property['terrace']){
                    $terrace = 1;
                }else{
                    $terrace = 0;
                }

                // Equipements
                $idcomplement = $property['idComplement'];
                $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
                //dd($equipments);

                // Récupération DPE & GES
                $bilanDpe = $propertyService->getClasseDpe($propriete);
                $bilanGes = $propertyService->getClasseGes($propriete);

                // Création d'une ligne du tableau
                $data = array(
                    '"RC1860977"',                                                  // 1 - Identifiant Agence
                    '"' . $property['ref'] . '"',                                               // 2 - Référence agence du bien
                    '"' . $destination['destination'] . '"',                        // 3 - Type d’annonce
                    '"' . $propertyService->getBien($property) . '"',                                             // 4 - Type de bien
                    '"' . $property['zipcode'] . '"',                               // 5 - CP
                    '"' . $property['city'] . '"',                                  // 6 - Ville
                    '"France"',                                                     // 7 - Pays
                    '"' . $property['adress'] . '"',                                // 8 - Adresse
                    '""',                                                           // 9 - Quartier / Proximité
                    '""',                                                           // 10 - Activités commerciales
                    '"' . $property['priceFai'] . '"',                              // 11 - Prix / Loyer / Prix de cession
                    '"' . $destination['rent'] . '"',                               // 12 - Loyer / mois murs
                    '"' . $destination['rentCC'] . '"',                             // 13 - Loyer CC
                    '"' . $destination['rentHT'] . '"',                             // 14 - Loyer HT
                    '"' . $destination['rentChargeHonoraire'] . '"',                // 15 - Honoraires
                    '"' . $property['surfaceHome'] . '"',                           // 16 - Surface (m²)
                    '"' . $property['surfaceLand'] . '"',                           // 17 - Surface terrain (m²)
                    '"' . $property['piece'] . '"',                                 // 18 - NB de pièces
                    '"' . $property['room'] . '"',                                  // 19 - NB de chambres
                    '"' . $property['name'] . '"',                                  // 20 - Libellé
                    '"' . $propertyService->getAnnonce($property) . '"',                                           // 21 - Descriptif
                    '"' . $disponibilityAt . '"',                                    // 22 - Date de disponibilité
                    '""',                                                           // 23 - Charges
                    '"' . $property['level'] . '"',                                 // 24 - Etage
                    '""',                                                           // 25 - NB d’étages
                    '"' . $property['isFurnished'] . '"',                           // 26 - Meublé
                    '"' . $property['constructionAt'] . '"',                        // 27 - Année de construction
                    '""',                                                           // 28 - Refait à neuf
                    '"' . $property['bathroom'] . '"',                              // 29 - NB de salles de bain
                    '"' . $property['sanitation'] . '"',                            // 30 - NB de salles d’eau
                    '"' . $property['wc'] . '"',                                    // 31 - NB de WC
                    '"0"',                                                          // 32 - WC séparés
                    '"' . $property['slCode'] . '"',                                // 33 - Type de chauffage
                    '""',                                                           // 34 - Type de cuisine
                    '"' . $sud . '"',                                               // 35 - Orientation sud
                    '"' . $est . '"',                                               // 36 - Orientation est
                    '"' . $ouest . '"',                                             // 37 - Orientation ouest
                    '"' . $nord . '"',                                              // 38 - Orientation nord
                    '"' . $property['balcony'] . '"',                               // 39 - NB balcons
                    '""',                                                           // 40 - SF Balcon
                    '"0"',                                                          // 41 - Ascenseur
                    '"0"',                                                          // 42 - Cave
                    '""',                                                           // 43 - NB de parkings
                    '"0"',                                                          // 44 - NB de boxes
                    '"0"',                                                          // 45 - Digicode
                    '"0"',                                                          // 46 - Interphone
                    '"0"',                                                          // 47 - Gardien
                    '"' . $terrace . '"',                                           // 48 - Terrasse
                    '""',                                                       // 49 - Prix semaine Basse Saison
                    '""',                                                       // 50 - Prix quinzaine Basse Saison
                    '""',                                                       // 51 - Prix mois / Basse Saison
                    '""',                                                       // 52 - Prix semaine Haute Saison
                    '""',                                                       // 53 - Prix quinzaine Haute Saison
                    '""',                                                       // 54 - Prix mois Haute Saison
                    '""',                                                       // 55 - NB de personnes
                    '""',                                                       // 56 - Type de résidence
                    '""',                                                       // 57 - Situation
                    '""',                                                       // 58 - NB de couverts
                    '""',                                                       // 59 - NB de lits doubles
                    '""',                                                       // 60 - NB de lits simples
                    '"0"',// 61 - Alarme
                    '"0"',// 62 - Câble TV
                    '"0"',// 63 - Calme
                    '"0"',// 64 - Climatisation
                    '"0"',// 65 - Piscine
                    '"0"',// 66 - Aménagement pour handicapés
                    '"0"',// 67 - Animaux acceptés
                    '"0"',// 68 - Cheminée
                    '"0"',// 69 - Congélateur
                    '"0"',// 70 - Four
                    '"0"',// 71 - Lave-vaisselle
                    '"0"',// 72 - Micro-ondes
                    '"0"',// 73 - Placards
                    '"0"',// 74 - Téléphone
                    '"0"',// 75 - Proche lac
                    '"0"',// 76 - Proche tennis
                    '"0"',// 77 - Proche pistes de ski
                    '"0"',// 78 - Vue dégagée
                    '""',                                       // 79 - Chiffre d’affaire
                    '""',                                       // 80 - Longueur façade (m)
                    '"0"',                                      // 81 - Duplex
                    '"' . $publications . '"',                                  // 82 - Publications
                    '"0"',                                      // 83 - Mandat en exclusivité
                    '"0"',                                      // 84 - Coup de cœur
                    '"' . $url1 . '"',                                              // 85 - Photo 1
                    '"' . $url2 . '"',                                              // 86 - Photo 2
                    '"' . $url3 . '"',                                              // 87 - Photo 3
                    '"' . $url4 . '"',                                              // 88 - Photo 4
                    '"' . $url5 . '"',                                              // 89 - Photo 5
                    '"' . $url6 . '"',                                              // 90 - Photo 6
                    '"' . $url7 . '"',                                              // 91 - Photo 7
                    '"' . $url8 . '"',                                              // 92 - Photo 8
                    '"' . $url9 . '"',                                              // 93 - Photo 9
                    '"' . $titrephoto1 . '"',                                       // 94 - Titre photo 1
                    '"' . $titrephoto2 . '"',                                       // 95 - Titre photo 2
                    '"' . $titrephoto3 . '"',                                       // 96 - Titre photo 3
                    '"' . $titrephoto4 . '"',                                       // 97 - Titre photo 4
                    '"' . $titrephoto5 . '"',                                       // 98 - Titre photo 5
                    '"' . $titrephoto6 . '"',                                       // 99 - Titre photo 6
                    '"' . $titrephoto7 . '"',                                       // 100 - Titre photo 7
                    '"' . $titrephoto8 . '"',                                       // 101 - Titre photo 8
                    '"' . $titrephoto9 . '"',                                       // 102 - Titre photo 9
                    '""',                                                       // 103 - Photo panoramique
                    '""',                                                       // 104 - URL visite virtuelle
                    '"' . $property['gsm'] . '"',                                   // 105 - Téléphone à afficher
                    '"' . $property['firstName'] . ' ' . $property['lastName'] . '"',   // 106 - Contact à afficher
                    '"' . $property['email'] . '"',                                 // 107 - Email de contact
                    '"' . $property['zipcode'] . '"',                               // 108 - CP Réel du bien
                    '"' . $property['city'] . '"',                                  // 109 - Ville réelle du bien
                    '""',                                                       // 110 - Inter-cabinet
                    '""',                                                       // 111 - Inter-cabinet prive
                    '"' . $refMandat . '"',                                  // 112 - N° de mandat
                    '"' . $mandatAt . '"',                                          // 113 - Date mandat
                    '""',                                                       // 114 - Nom mandataire
                    '""',                                                       // 115 - Prénom mandataire
                    '""',                                                       // 116 - Raison sociale mandataire
                    '""',                                                       // 117 - Adresse mandataire
                    '""',                                                       // 118 - CP mandataire
                    '""',                                                       // 119 - Ville mandataire
                    '""',                                                       // 120 - Téléphone mandataire
                    '""',                                                       // 121 - Commentaires mandataire
                    '""',                                                       // 122 - Commentaires privés
                    '""',                                                       // 123 - Code négociateur
                    '""',                                                       // 124 - Code Langue 1
                    '""',                                                       // 125 - Proximité Langue 1
                    '""',                                                       // 126 - Libellé Langue 1
                    '""',                                                       // 127 - Descriptif Langue 1
                    '""',                                                       // 128 - Code Langue 2
                    '""',                                                       // 129 - Proximité Langue 2
                    '""',                                                       // 130 - Libellé Langue 2
                    '""',                                                       // 131 - Descriptif Langue 2
                    '""',                                                       // 132 - Code Langue 3
                    '""',                                                       // 133 - Proximité Langue 3
                    '""',                                                       // 134 - Libellé Langue 3
                    '""',                                                       // 135 - Descriptif Langue 3
                    '""',                                                       // 136 - Champ personnalisé 1
                    '""',                                                       // 137 - Champ personnalisé 2
                    '""',                                                       // 138 - Champ personnalisé 3
                    '""',                                                       // 139 - Champ personnalisé 4
                    '""',                                                       // 140 - Champ personnalisé 5
                    '""',                                                       // 141 - Champ personnalisé 6
                    '""',                                                       // 142 - Champ personnalisé 7
                    '""',                                                       // 143 - Champ personnalisé 8
                    '""',                                                       // 144 - Champ personnalisé 9
                    '""',                                                       // 145 - Champ personnalisé 10
                    '""',                                                       // 146 - Champ personnalisé 11
                    '""',                                                       // 147 - Champ personnalisé 12
                    '""',                                                       // 148 - Champ personnalisé 13
                    '""',                                                       // 149 - Champ personnalisé 14
                    '""',                                                       // 150 - Champ personnalisé 15
                    '""',                                                       // 151 - Champ personnalisé 16
                    '""',                                                       // 152 - Champ personnalisé 17
                    '""',                                                       // 153 - Champ personnalisé 18
                    '""',                                                       // 154 - Champ personnalisé 19
                    '""',                                                       // 155 - Champ personnalisé 20
                    '""',                                                       // 156 - Champ personnalisé 21
                    '""',                                                       // 157 - Champ personnalisé 22
                    '""',                                                       // 158 - Champ personnalisé 23
                    '""',                                                       // 159 - Champ personnalisé 24
                    '""',                                                       // 160 - Champ personnalisé 25
                    '""',                                                       // 161 - Dépôt de garantie
                    '"0"',                                                      // 162 - Récent
                    '"0"',                                                      // 163 - Travaux à prévoir
                    '"' . $url10 . '"',                                             // 164 - Photo 10
                    '"' . $url11 . '"',                                             // 165 - Photo 11
                    '"' . $url12 . '"',                                             // 166 - Photo 12
                    '"' . $url13 . '"',                                             // 167 - Photo 13
                    '"' . $url14 . '"',                                             // 168 - Photo 14
                    '"' . $url15 . '"',                                             // 169 - Photo 15
                    '"' . $url16 . '"',                                             // 170 - Photo 16
                    '"' . $url17 . '"',                                             // 171 - Photo 17
                    '"' . $url18 . '"',                                             // 172 - Photo 18
                    '"' . $url19 . '"',                                             // 173 - Photo 19
                    '"' . $url20 . '"',                                             // 174 - Photo 20
                    '""',                                                       // 175 - Identifiant technique
                    '"' . $property['diagDpe'] . '"',                               // 176 - Consommation énergie
                    '"' . $bilanDpe . '"',                                          // 177 - Bilan consommation énergie
                    '"' . $property['diagGes'] . '"',                               // 178 - Emissions GES
                    '"' . $bilanGes . '"',                                          // 179 - Bilan émission GES
                    '""',                                                       // 180 - Identifiant quartier (obsolète)
                    '"' . $property['ssCategory'] . '"',                            // 181 - Sous type de bien
                    '""',                                                       // 182 - Périodes de disponibilité
                    '""',                                                       // 183 - Périodes basse saison
                    '""',                                                       // 184 - Périodes haute saison
                    '""',                                                       // 185 - Prix du bouquet
                    '""',                                                       // 186 - Rente mensuelle
                    '""',                                                       // 187 - Age de l’homme
                    '""',                                                       // 188 - Age de la femme
                    '"0"',                                                      // 189 - Entrée
                    '"0"',                                                      // 190 - Résidence
                    '"0"',                                                      // 191 - Parquet
                    '"0"',                                                      // 192 - Vis-à-vis
                    '""',                                                       // 193 - Transport : Ligne
                    '""',                                                       // 194 - Transport : Station
                    '""',                                                       // 195 - Durée bail
                    '""',                                                       // 196 - Places en salle
                    '""',                                                       // 197 - Monte-charge
                    '""',                                                       // 198 - Quai
                    '""',                                                       // 199 - Nombre de bureaux
                    '""',                                                       // 200 - Prix du droit d’entrée
                    '""',                                                       // 201 - Prix masqué
                    '"'.$destination['commerceAnnualRentGlobal'].'"',           // 202 - Loyer annuel global
                    '"'.$destination['commerceAnnualChargeRentGlobal'].'"',     // 203 - Charges annuelles globales
                    '"'.$destination['commerceAnnualRentMeter'].'"',            // 204 - Loyer annuel au m2
                    '"'.$destination['commerceAnnualChargeRentMeter'].'"',      // 205 - Charges annuelles au m2
                    '"'.$destination['commerceChargeRentMonthHt'].'"',          // 206 - Charges mensuelles  Loyer annuel CC HT
                    '"'.$destination['commerceRentAnnualCc'].'"',               // 207 - Loyer annuel CC
                    '"'.$destination['commerceRentAnnualHt'].'"',               // 208 - Loyer annuel HT
                    '"'.$destination['commerceChargeRentAnnualHt'].'"',         // 209 - Charges annuelles HT
                    '"'.$destination['commerceRentAnnualMeterCc'].'"',          // 210 - Loyer annuel au m2 CC
                    '"'.$destination['commerceRentAnnualMeterHt'].'"',          // 211 - Loyer annuel au m2 HT
                    '"'.$destination['commerceChargeRentAnnualMeterHt'].'"',    // 212 - Charges annuelles au m2 HT
                    '"'.$destination['commerceSurfaceDivisible'].'"',           // 213 - Divisible
                    '"'.$destination['commerceSurfaceDivisibleMin'].'"',        // 214 - Surface divisible minimale
                    '"'.$destination['commerceSurfaceDivisibleMax'].'"',        // 215 - Surface divisible maximale
                    '""',                                   // 216 - Surface séjour
                    '""',                                   // 217 - Nombre de véhicules
                    '""',                                   // 218 - Prix du droit au bail
                    '""',                                   // 219 - Valeur à l’achat
                    '""',                                   // 220 - Répartition du chiffre d’affaire
                    '""',                                   // 221 - Terrain agricole
                    '""',                                   // 222 - Equipement bébé
                    '""',                                   // 223 - Terrain constructible
                    '""',                                   // 224 - Résultat Année N-2
                    '""',                                   // 225 - Résultat Année N-1
                    '""',                                   // 226 - Résultat Actuel
                    '""',                                   // 227 - Immeuble de parkings
                    '""',                                   // 228 - Parking isolé
                    '""',                                   // 229 - Si Viager Vendu Libre Logement à
                    '""',                                   // 230 - Logement à disposition
                    '""',                                   // 231 - Terrain en pente
                    '""',                                   // 232 - Plan d’eau
                    '""',                                   // 233 - Lave-linge
                    '""',                                   // 234 - Sèche-linge
                    '""',                                   // 235 - Connexion internet
                    '""',                                   // 236 - Chiffre affaire Année N-2
                    '""',                                   // 237 - Chiffre affaire Année N-1
                    '""',                                   // 238 - Conditions financières
                    '""',                                   // 239 - Prestations diverses
                    '""',                                   // 240 - Longueur façade
                    '""',                                   // 241 - Montant du rapport
                    '""',                                   // 242 - Nature du bail
                    '""',                                   // 243 - Nature bail commercial
                    '""',                                   // 244 - Nombre terrasses
                    '""',                                   // 245 - Prix hors taxes
                    '""',                                   // 246 - Si Salle à manger
                    '""',                                   // 247 - Si Séjour
                    '""',                                   // 248 - Terrain donne sur la rue
                    '""',                                   // 249 - Immeuble de type bureaux
                    '""',                                   // 250 - Terrain viabilisé
                    '""',                                   // 251 - Equipement Vidéo
                    '""',                                   // 252 - Surface de la cave
                    '""',                                   // 253 - Surface de la salle à manger
                    '""',                                   // 254 - Situation commerciale
                    '""',                                   // 255 - Surface maximale d’un bureau
                    '""',                                   // 256 - Honoraires charge acquéreur (obsolète)
                    '""',                                   // 257 - Pourcentage honoraires TTC (obsolète)
                    '"' . $property['copro'] . '"',                                 // 258 - En copropriété
                    '""',                                   // 259 - Nombre de lots
                    '"' . $property['chargeCopro'] . '"',                           // 260 - Charges annuelles
                    '""',                                   // 261 - Syndicat des copropriétaires en procédure
                    '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
                    '""',                                   // 263 - Champ personnalisé 26
                    '"' . $url21 . '"',                                             // 264 - Photo 21
                    '"' . $url22 . '"',                                             // 265 - Photo 22
                    '"' . $url23 . '"',                                             // 266 - Photo 23
                    '"' . $url24 . '"',                                             // 267 - Photo 24
                    '"' . $url25 . '"',                                             // 268 - Photo 25
                    '"' . $url26 . '"',                                             // 269 - Photo 26
                    '"' . $url27 . '"',                                             // 270 - Photo 27
                    '"' . $url28 . '"',                                             // 271 - Photo 28
                    '"' . $url29 . '"',                                             // 272 - Photo 29
                    '"' . $url30 . '"',                                             // 273 - Photo 30
                    '"' . $titrephoto10 . '"',                                      // 274 - Titre photo 10
                    '"' . $titrephoto11 . '"',                                      // 275 - Titre photo 11
                    '"' . $titrephoto12 . '"',                                      // 276 - Titre photo 12
                    '"' . $titrephoto13 . '"',                                      // 277 - Titre photo 13
                    '"' . $titrephoto14 . '"',                                      // 278 - Titre photo 14
                    '"' . $titrephoto15 . '"',                                      // 279 - Titre photo 15
                    '"' . $titrephoto16 . '"',                                      // 280 - Titre photo 16
                    '"' . $titrephoto17 . '"',                                      // 281 - Titre photo 17
                    '"' . $titrephoto18 . '"',                                      // 282 - Titre photo 18
                    '"' . $titrephoto19 . '"',                                      // 283 - Titre photo 19
                    '"' . $titrephoto20 . '"',                                      // 284 - Titre photo 20
                    '"' . $titrephoto21 . '"',                                      // 285 - Titre photo 21
                    '"' . $titrephoto22 . '"',                                      // 286 - Titre photo 22
                    '"' . $titrephoto23 . '"',                                      // 287 - Titre photo 23
                    '"' . $titrephoto24 . '"',                                      // 288 - Titre photo 24
                    '"' . $titrephoto25 . '"',                                      // 289 - Titre photo 25
                    '"' . $titrephoto26 . '"',                                      // 290 - Titre photo 26
                    '"' . $titrephoto27 . '"',                                      // 291 - Titre photo 27
                    '"' . $titrephoto28 . '"',                                      // 292 - Titre photo 28
                    '"' . $titrephoto29 . '"',                                      // 293 - Titre photo 29
                    '"' . $titrephoto30 . '"',                                      // 294 - Titre photo 30
                    '""',// 295 - Prix du terrain
                    '""',// 296 - Prix du modèle de maison
                    '""',// 297 - Nom de l'agence gérant le terrain
                    '""',// 298 - Latitude
                    '""',// 299 - Longitude
                    '""',// 300 - Précision GPS
                    '"4.11"',// 301 - Version Format
                    '""',// 302 - Honoraires à la charge de l'acquéreur
                    '""',// 303 - Prix hors honoraires acquéreur
                    '""',// 304 - Modalités charges locataire
                    '""',// 305 - Complément loyer
                    '""',// 306 - Part honoraires état des lieux
                    '""',// 307 - URL du Barème des honoraires de l’Agence
                    '""',// 308 - Prix minimum
                    '""',// 309 - Prix maximum
                    '""',// 310 - Surface minimale
                    '""',// 311 - Surface maximale
                    '""',// 312 - Nombre de pièces minimum
                    '""',// 313 - Nombre de pièces maximum
                    '""',// 314 - Nombre de chambres minimum
                    '""',// 315 - Nombre de chambres maximum
                    '""',// 316 - ID type étage
                    '""',// 317 - Si combles aménageables
                    '""',// 318 - Si garage
                    '""',// 319 - ID type garage
                    '""',// 320 - Si possibilité mitoyenneté
                    '""',// 321 - Surface terrain nécessaire
                    '""',// 322 - Localisation
                    '""',// 323 - Nom du modèle
                    '"' . $dpeAt . '"',                                             // 324 - Date réalisation DPE
                    '""',                                                       // 325 - Version DPE
                    '"' . $property['dpeEstimateEnergyDown'] . '"',                 // 326 - DPE coût min conso
                    '"' . $property['dpeEstimateEnergyUp'] . '"',                   // 327 - DPE coût max conso
                    '"' . $RefDPE . '"',                                            // 328 - DPE date référence conso
                    '""',                                                       // 329 - Surface terrasse
                    '""',                                                       // 330 - DPE coût conso annuelle
                    '""',                                                       // 331 - Loyer de base
                    '""',                                                       // 332 - Loyer de référence majoré
                    '""',                                                       // 333 - Encadrement des loyers
                );
                $rows[] = implode('!#', $data);
            }
            $content = implode("\n", $rows);
            dd($content);
        }
        elseif ($diffuseur == 'FIG'){
            $properties = $propertyRepository->reportpropertyfigaroFTP();

            // Création de l'url pour les photos
            $fullHttp = $request->getUri();
            $scheme = parse_url($fullHttp, PHP_URL_SCHEME);
            $port = parse_url($fullHttp, PHP_URL_PORT);
            $host = parse_url($fullHttp, PHP_URL_HOST);
            //dd($scheme, $port, $host);
            if (!$port){
                $app = $scheme.'://'.$host;
            }else{
                $app = $scheme.'://'.$host.':'.$port;
            }

            $rows = array();
            foreach ($properties as $property){
                $propriete = $propertyRepository->find($property['id']);
                //destination du bien
                $destination = $propertyService->getDestination($propriete);
                // Description de l'annonce
                $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
                $annonce = strip_tags($data, '<br>');
                //dd($annonce);

                // Récupération de la reference
                $ref = $property['ref'];
                $refMandat = $property['refMandat'];

                // Sélection du type de bien
                $bien = $propertyService->getBien($property);

                // Préparation de la date dpeAt
                if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                    $dpeAt = $property['dpeAt']->format('d/m/Y');
                }else{
                    $dpeAt ="";
                }

                // Préparation de la date de réation mandat
                if ($property['mandatAt'] && $property['mandatAt'] instanceof \DateTime) {
                    $mandatAt = $property['mandatAt']->format('d/m/Y');
                }else{
                    $mandatAt ="";
                }

                // Préparation de la date de création RefDPE
                if ($property['RefDPE'] && $property['RefDPE'] instanceof \DateTime) {
                    $RefDPE = $property['RefDPE']->format('d/m/Y');
                }else{
                    $RefDPE ="";
                }

                // Préparation de la date de disponibilité
                if ($property['disponibilityAt'] instanceof \DateTime) {
                    $disponibilityAt = $property['disponibilityAt']->format('d/m/Y');
                }else{
                    $disponibilityAt ="";
                }

                // Calcul des honoraires en %
                //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
                //dd($property['price'], $property['priceFai'], $honoraires);

                // Récupération des images liées au bien
                $photos = $photoRepository->findNameBy(['property' => $property['id']]);
                if(!$photos){                                                                       // Si aucune photo présente
                    $url = [];
                    $titrephoto = [];
                    for ($i = 1; $i<31; $i++){
                        ${'url'.$i} = '';
                        array_push($url, ${'url'.$i});
                    }
                    // génération des titres de photos
                    for ($i = 1; $i<31; $i++){
                        ${'titrephoto'.$i} = '';
                        array_push($titrephoto, ${'titrephoto'.$i});
                    }
                }else{
                    $url = [];
                    $arraykey = array_keys($photos);
                    for ($key = 0; $key<30; $key++){
                        if(array_key_exists($key,$arraykey)){
                            ${'url'.$key+1} = $app.'/properties/'.$photos[$key]['path'].'/'.$photos[$key]['galeryFrontName']."?".$photos[$key]['createdAt']->format('Ymd');
                            array_push($url, ${'url'.$key+1});
                        }else{
                            ${'url'.$key+1} = '';
                            array_push($url, ${'url'.$key+1});
                        }
                    }
                    // génération des titres de photos
                    for ($key = 0; $key<30; $key++){
                        if(array_key_exists($key,$arraykey)){
                            ${'titrephoto'.$key+1} = 'Photo-'.$property['ref'].'-'.$key+1;
                            array_push($url, ${'titrephoto'.$key+1});
                        }else{
                            ${'titrephoto'.$key+1} = '';
                            array_push($url, ${'titrephoto'.$key+1});
                        }
                    }
                }

                // Orientation
                $orientation = $property['orientation'];
                if($orientation = 'nord'){
                    $nord = 1;
                    $est = 0;
                    $sud = 0;
                    $ouest = 0;
                }elseif($orientation = 'est'){
                    $nord = 0;
                    $est = 1;
                    $sud = 0;
                    $ouest = 0;
                }elseif($orientation = 'sud'){
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

                // Transformation terrace en booléen
                if($property['terrace']){
                    $terrace = 1;
                }else{
                    $terrace = 0;
                }

                // Equipements
                $idcomplement = $property['idComplement'];
                $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
                //dd($equipments);

                // Récupération DPE & GES
                $bilanDpe = $propertyService->getClasseDpe($propriete);
                $bilanGes = $propertyService->getClasseGes($propriete);

                // Création d'une ligne du tableau
                $data = array(
                    '"RC1860977"',                                                  // 1 - Identifiant Agence
                    '"' . $ref . '"',                                               // 2 - Référence agence du bien
                    '"' . $destination['destination'] . '"',                        // 3 - Type d’annonce
                    '"' . $bien . '"',                                             // 4 - Type de bien
                    '"' . $property['zipcode'] . '"',                               // 5 - CP
                    '"' . $property['city'] . '"',                                  // 6 - Ville
                    '"France"',                                                     // 7 - Pays
                    '"' . $property['adress'] . '"',                                // 8 - Adresse
                    '""',                                                           // 9 - Quartier / Proximité
                    '""',                                                           // 10 - Activités commerciales
                    '"' . $property['priceFai'] . '"',                              // 11 - Prix / Loyer / Prix de cession
                    '"' . $destination['rent'] . '"',                               // 12 - Loyer / mois murs
                    '"' . $destination['rentCC'] . '"',                             // 13 - Loyer CC
                    '"' . $destination['rentHT'] . '"',                             // 14 - Loyer HT
                    '"' . $destination['rentChargeHonoraire'] . '"',                // 15 - Honoraires
                    '"' . $property['surfaceHome'] . '"',                           // 16 - Surface (m²)
                    '"' . $property['surfaceLand'] . '"',                           // 17 - Surface terrain (m²)
                    '"' . $property['piece'] . '"',                                 // 18 - NB de pièces
                    '"' . $property['room'] . '"',                                  // 19 - NB de chambres
                    '"' . $property['name'] . '"',                                  // 20 - Libellé
                    '"' . $annonce . '"',                                           // 21 - Descriptif
                    '"' . $disponibilityAt . '"',                                    // 22 - Date de disponibilité
                    '""',                                                           // 23 - Charges
                    '"' . $property['level'] . '"',                                 // 24 - Etage
                    '""',                                                           // 25 - NB d’étages
                    '"' . $property['isFurnished'] . '"',                           // 26 - Meublé
                    '"' . $property['constructionAt'] . '"',                        // 27 - Année de construction
                    '""',                                                           // 28 - Refait à neuf
                    '"' . $property['bathroom'] . '"',                              // 29 - NB de salles de bain
                    '"' . $property['sanitation'] . '"',                            // 30 - NB de salles d’eau
                    '"' . $property['wc'] . '"',                                    // 31 - NB de WC
                    '"0"',                                                          // 32 - WC séparés
                    '"' . $property['slCode'] . '"',                                // 33 - Type de chauffage
                    '""',                                                           // 34 - Type de cuisine
                    '"' . $sud . '"',                                               // 35 - Orientation sud
                    '"' . $est . '"',                                               // 36 - Orientation est
                    '"' . $ouest . '"',                                             // 37 - Orientation ouest
                    '"' . $nord . '"',                                              // 38 - Orientation nord
                    '"' . $property['balcony'] . '"',                               // 39 - NB balcons
                    '""',                                                           // 40 - SF Balcon
                    '"0"',                                                          // 41 - Ascenseur
                    '"0"',                                                          // 42 - Cave
                    '""',                                                           // 43 - NB de parkings
                    '"0"',                                                          // 44 - NB de boxes
                    '"0"',                                                          // 45 - Digicode
                    '"0"',                                                          // 46 - Interphone
                    '"0"',                                                          // 47 - Gardien
                    '"' . $terrace . '"',                                           // 48 - Terrasse
                    '""',                                                       // 49 - Prix semaine Basse Saison
                    '""',                                                       // 50 - Prix quinzaine Basse Saison
                    '""',                                                       // 51 - Prix mois / Basse Saison
                    '""',                                                       // 52 - Prix semaine Haute Saison
                    '""',                                                       // 53 - Prix quinzaine Haute Saison
                    '""',                                                       // 54 - Prix mois Haute Saison
                    '""',                                                       // 55 - NB de personnes
                    '""',                                                       // 56 - Type de résidence
                    '""',                                                       // 57 - Situation
                    '""',                                                       // 58 - NB de couverts
                    '""',                                                       // 59 - NB de lits doubles
                    '""',                                                       // 60 - NB de lits simples
                    '"0"',// 61 - Alarme
                    '"0"',// 62 - Câble TV
                    '"0"',// 63 - Calme
                    '"0"',// 64 - Climatisation
                    '"0"',// 65 - Piscine
                    '"0"',// 66 - Aménagement pour handicapés
                    '"0"',// 67 - Animaux acceptés
                    '"0"',// 68 - Cheminée
                    '"0"',// 69 - Congélateur
                    '"0"',// 70 - Four
                    '"0"',// 71 - Lave-vaisselle
                    '"0"',// 72 - Micro-ondes
                    '"0"',// 73 - Placards
                    '"0"',// 74 - Téléphone
                    '"0"',// 75 - Proche lac
                    '"0"',// 76 - Proche tennis
                    '"0"',// 77 - Proche pistes de ski
                    '"0"',// 78 - Vue dégagée
                    '""',                                       // 79 - Chiffre d’affaire
                    '""',                                       // 80 - Longueur façade (m)
                    '"0"',                                      // 81 - Duplex
                    '"' . $publications . '"',                                  // 82 - Publications
                    '"0"',                                      // 83 - Mandat en exclusivité
                    '"0"',                                      // 84 - Coup de cœur
                    '"' . $url1 . '"',                                              // 85 - Photo 1
                    '"' . $url2 . '"',                                              // 86 - Photo 2
                    '"' . $url3 . '"',                                              // 87 - Photo 3
                    '"' . $url4 . '"',                                              // 88 - Photo 4
                    '"' . $url5 . '"',                                              // 89 - Photo 5
                    '"' . $url6 . '"',                                              // 90 - Photo 6
                    '"' . $url7 . '"',                                              // 91 - Photo 7
                    '"' . $url8 . '"',                                              // 92 - Photo 8
                    '"' . $url9 . '"',                                              // 93 - Photo 9
                    '"' . $titrephoto1 . '"',                                       // 94 - Titre photo 1
                    '"' . $titrephoto2 . '"',                                       // 95 - Titre photo 2
                    '"' . $titrephoto3 . '"',                                       // 96 - Titre photo 3
                    '"' . $titrephoto4 . '"',                                       // 97 - Titre photo 4
                    '"' . $titrephoto5 . '"',                                       // 98 - Titre photo 5
                    '"' . $titrephoto6 . '"',                                       // 99 - Titre photo 6
                    '"' . $titrephoto7 . '"',                                       // 100 - Titre photo 7
                    '"' . $titrephoto8 . '"',                                       // 101 - Titre photo 8
                    '"' . $titrephoto9 . '"',                                       // 102 - Titre photo 9
                    '""',                                                       // 103 - Photo panoramique
                    '""',                                                       // 104 - URL visite virtuelle
                    '"' . $property['gsm'] . '"',                                   // 105 - Téléphone à afficher
                    '"' . $property['firstName'] . ' ' . $property['lastName'] . '"',   // 106 - Contact à afficher
                    '"' . $property['email'] . '"',                                 // 107 - Email de contact
                    '"' . $property['zipcode'] . '"',                               // 108 - CP Réel du bien
                    '"' . $property['city'] . '"',                                  // 109 - Ville réelle du bien
                    '""',                                                       // 110 - Inter-cabinet
                    '""',                                                       // 111 - Inter-cabinet prive
                    '"' . $refMandat . '"',                                  // 112 - N° de mandat
                    '"' . $mandatAt . '"',                                          // 113 - Date mandat
                    '""',                                                       // 114 - Nom mandataire
                    '""',                                                       // 115 - Prénom mandataire
                    '""',                                                       // 116 - Raison sociale mandataire
                    '""',                                                       // 117 - Adresse mandataire
                    '""',                                                       // 118 - CP mandataire
                    '""',                                                       // 119 - Ville mandataire
                    '""',                                                       // 120 - Téléphone mandataire
                    '""',                                                       // 121 - Commentaires mandataire
                    '""',                                                       // 122 - Commentaires privés
                    '""',                                                       // 123 - Code négociateur
                    '""',                                                       // 124 - Code Langue 1
                    '""',                                                       // 125 - Proximité Langue 1
                    '""',                                                       // 126 - Libellé Langue 1
                    '""',                                                       // 127 - Descriptif Langue 1
                    '""',                                                       // 128 - Code Langue 2
                    '""',                                                       // 129 - Proximité Langue 2
                    '""',                                                       // 130 - Libellé Langue 2
                    '""',                                                       // 131 - Descriptif Langue 2
                    '""',                                                       // 132 - Code Langue 3
                    '""',                                                       // 133 - Proximité Langue 3
                    '""',                                                       // 134 - Libellé Langue 3
                    '""',                                                       // 135 - Descriptif Langue 3
                    '""',                                                       // 136 - Champ personnalisé 1
                    '""',                                                       // 137 - Champ personnalisé 2
                    '""',                                                       // 138 - Champ personnalisé 3
                    '""',                                                       // 139 - Champ personnalisé 4
                    '""',                                                       // 140 - Champ personnalisé 5
                    '""',                                                       // 141 - Champ personnalisé 6
                    '""',                                                       // 142 - Champ personnalisé 7
                    '""',                                                       // 143 - Champ personnalisé 8
                    '""',                                                       // 144 - Champ personnalisé 9
                    '""',                                                       // 145 - Champ personnalisé 10
                    '""',                                                       // 146 - Champ personnalisé 11
                    '""',                                                       // 147 - Champ personnalisé 12
                    '""',                                                       // 148 - Champ personnalisé 13
                    '""',                                                       // 149 - Champ personnalisé 14
                    '""',                                                       // 150 - Champ personnalisé 15
                    '""',                                                       // 151 - Champ personnalisé 16
                    '""',                                                       // 152 - Champ personnalisé 17
                    '""',                                                       // 153 - Champ personnalisé 18
                    '""',                                                       // 154 - Champ personnalisé 19
                    '""',                                                       // 155 - Champ personnalisé 20
                    '""',                                                       // 156 - Champ personnalisé 21
                    '""',                                                       // 157 - Champ personnalisé 22
                    '""',                                                       // 158 - Champ personnalisé 23
                    '""',                                                       // 159 - Champ personnalisé 24
                    '""',                                                       // 160 - Champ personnalisé 25
                    '""',                                                       // 161 - Dépôt de garantie
                    '"0"',                                                      // 162 - Récent
                    '"0"',                                                      // 163 - Travaux à prévoir
                    '"' . $url10 . '"',                                             // 164 - Photo 10
                    '"' . $url11 . '"',                                             // 165 - Photo 11
                    '"' . $url12 . '"',                                             // 166 - Photo 12
                    '"' . $url13 . '"',                                             // 167 - Photo 13
                    '"' . $url14 . '"',                                             // 168 - Photo 14
                    '"' . $url15 . '"',                                             // 169 - Photo 15
                    '"' . $url16 . '"',                                             // 170 - Photo 16
                    '"' . $url17 . '"',                                             // 171 - Photo 17
                    '"' . $url18 . '"',                                             // 172 - Photo 18
                    '"' . $url19 . '"',                                             // 173 - Photo 19
                    '"' . $url20 . '"',                                             // 174 - Photo 20
                    '""',                                                       // 175 - Identifiant technique
                    '"' . $property['diagDpe'] . '"',                               // 176 - Consommation énergie
                    '"' . $bilanDpe . '"',                                          // 177 - Bilan consommation énergie
                    '"' . $property['diagGes'] . '"',                               // 178 - Emissions GES
                    '"' . $bilanGes . '"',                                          // 179 - Bilan émission GES
                    '""',                                                       // 180 - Identifiant quartier (obsolète)
                    '"' . $property['ssCategory'] . '"',                            // 181 - Sous type de bien
                    '""',                                                       // 182 - Périodes de disponibilité
                    '""',                                                       // 183 - Périodes basse saison
                    '""',                                                       // 184 - Périodes haute saison
                    '""',                                                       // 185 - Prix du bouquet
                    '""',                                                       // 186 - Rente mensuelle
                    '""',                                                       // 187 - Age de l’homme
                    '""',                                                       // 188 - Age de la femme
                    '"0"',                                                      // 189 - Entrée
                    '"0"',                                                      // 190 - Résidence
                    '"0"',                                                      // 191 - Parquet
                    '"0"',                                                      // 192 - Vis-à-vis
                    '""',                                                       // 193 - Transport : Ligne
                    '""',                                                       // 194 - Transport : Station
                    '""',                                                       // 195 - Durée bail
                    '""',                                                       // 196 - Places en salle
                    '""',                                                       // 197 - Monte-charge
                    '""',                                                       // 198 - Quai
                    '""',                                                       // 199 - Nombre de bureaux
                    '""',                                                       // 200 - Prix du droit d’entrée
                    '""',                                                       // 201 - Prix masqué
                    '"'.$destination['commerceAnnualRentGlobal'].'"',           // 202 - Loyer annuel global
                    '"'.$destination['commerceAnnualChargeRentGlobal'].'"',     // 203 - Charges annuelles globales
                    '"'.$destination['commerceAnnualRentMeter'].'"',            // 204 - Loyer annuel au m2
                    '"'.$destination['commerceAnnualChargeRentMeter'].'"',      // 205 - Charges annuelles au m2
                    '"'.$destination['commerceChargeRentMonthHt'].'"',          // 206 - Charges mensuelles  Loyer annuel CC HT
                    '"'.$destination['commerceRentAnnualCc'].'"',               // 207 - Loyer annuel CC
                    '"'.$destination['commerceRentAnnualHt'].'"',               // 208 - Loyer annuel HT
                    '"'.$destination['commerceChargeRentAnnualHt'].'"',         // 209 - Charges annuelles HT
                    '"'.$destination['commerceRentAnnualMeterCc'].'"',          // 210 - Loyer annuel au m2 CC
                    '"'.$destination['commerceRentAnnualMeterHt'].'"',          // 211 - Loyer annuel au m2 HT
                    '"'.$destination['commerceChargeRentAnnualMeterHt'].'"',    // 212 - Charges annuelles au m2 HT
                    '"'.$destination['commerceSurfaceDivisible'].'"',           // 213 - Divisible
                    '"'.$destination['commerceSurfaceDivisibleMin'].'"',        // 214 - Surface divisible minimale
                    '"'.$destination['commerceSurfaceDivisibleMax'].'"',        // 215 - Surface divisible maximale
                    '""',                                   // 216 - Surface séjour
                    '""',                                   // 217 - Nombre de véhicules
                    '""',                                   // 218 - Prix du droit au bail
                    '""',                                   // 219 - Valeur à l’achat
                    '""',                                   // 220 - Répartition du chiffre d’affaire
                    '""',                                   // 221 - Terrain agricole
                    '""',                                   // 222 - Equipement bébé
                    '""',                                   // 223 - Terrain constructible
                    '""',                                   // 224 - Résultat Année N-2
                    '""',                                   // 225 - Résultat Année N-1
                    '""',                                   // 226 - Résultat Actuel
                    '""',                                   // 227 - Immeuble de parkings
                    '""',                                   // 228 - Parking isolé
                    '""',                                   // 229 - Si Viager Vendu Libre Logement à
                    '""',                                   // 230 - Logement à disposition
                    '""',                                   // 231 - Terrain en pente
                    '""',                                   // 232 - Plan d’eau
                    '""',                                   // 233 - Lave-linge
                    '""',                                   // 234 - Sèche-linge
                    '""',                                   // 235 - Connexion internet
                    '""',                                   // 236 - Chiffre affaire Année N-2
                    '""',                                   // 237 - Chiffre affaire Année N-1
                    '""',                                   // 238 - Conditions financières
                    '""',                                   // 239 - Prestations diverses
                    '""',                                   // 240 - Longueur façade
                    '""',                                   // 241 - Montant du rapport
                    '""',                                   // 242 - Nature du bail
                    '""',                                   // 243 - Nature bail commercial
                    '""',                                   // 244 - Nombre terrasses
                    '""',                                   // 245 - Prix hors taxes
                    '""',                                   // 246 - Si Salle à manger
                    '""',                                   // 247 - Si Séjour
                    '""',                                   // 248 - Terrain donne sur la rue
                    '""',                                   // 249 - Immeuble de type bureaux
                    '""',                                   // 250 - Terrain viabilisé
                    '""',                                   // 251 - Equipement Vidéo
                    '""',                                   // 252 - Surface de la cave
                    '""',                                   // 253 - Surface de la salle à manger
                    '""',                                   // 254 - Situation commerciale
                    '""',                                   // 255 - Surface maximale d’un bureau
                    '""',                                   // 256 - Honoraires charge acquéreur (obsolète)
                    '""',                                   // 257 - Pourcentage honoraires TTC (obsolète)
                    '"' . $property['copro'] . '"',                                 // 258 - En copropriété
                    '""',                                   // 259 - Nombre de lots
                    '"' . $property['chargeCopro'] . '"',                           // 260 - Charges annuelles
                    '""',                                   // 261 - Syndicat des copropriétaires en procédure
                    '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
                    '""',                                   // 263 - Champ personnalisé 26
                    '"' . $url21 . '"',                                             // 264 - Photo 21
                    '"' . $url22 . '"',                                             // 265 - Photo 22
                    '"' . $url23 . '"',                                             // 266 - Photo 23
                    '"' . $url24 . '"',                                             // 267 - Photo 24
                    '"' . $url25 . '"',                                             // 268 - Photo 25
                    '"' . $url26 . '"',                                             // 269 - Photo 26
                    '"' . $url27 . '"',                                             // 270 - Photo 27
                    '"' . $url28 . '"',                                             // 271 - Photo 28
                    '"' . $url29 . '"',                                             // 272 - Photo 29
                    '"' . $url30 . '"',                                             // 273 - Photo 30
                    '"' . $titrephoto10 . '"',                                      // 274 - Titre photo 10
                    '"' . $titrephoto11 . '"',                                      // 275 - Titre photo 11
                    '"' . $titrephoto12 . '"',                                      // 276 - Titre photo 12
                    '"' . $titrephoto13 . '"',                                      // 277 - Titre photo 13
                    '"' . $titrephoto14 . '"',                                      // 278 - Titre photo 14
                    '"' . $titrephoto15 . '"',                                      // 279 - Titre photo 15
                    '"' . $titrephoto16 . '"',                                      // 280 - Titre photo 16
                    '"' . $titrephoto17 . '"',                                      // 281 - Titre photo 17
                    '"' . $titrephoto18 . '"',                                      // 282 - Titre photo 18
                    '"' . $titrephoto19 . '"',                                      // 283 - Titre photo 19
                    '"' . $titrephoto20 . '"',                                      // 284 - Titre photo 20
                    '"' . $titrephoto21 . '"',                                      // 285 - Titre photo 21
                    '"' . $titrephoto22 . '"',                                      // 286 - Titre photo 22
                    '"' . $titrephoto23 . '"',                                      // 287 - Titre photo 23
                    '"' . $titrephoto24 . '"',                                      // 288 - Titre photo 24
                    '"' . $titrephoto25 . '"',                                      // 289 - Titre photo 25
                    '"' . $titrephoto26 . '"',                                      // 290 - Titre photo 26
                    '"' . $titrephoto27 . '"',                                      // 291 - Titre photo 27
                    '"' . $titrephoto28 . '"',                                      // 292 - Titre photo 28
                    '"' . $titrephoto29 . '"',                                      // 293 - Titre photo 29
                    '"' . $titrephoto30 . '"',                                      // 294 - Titre photo 30
                    '""',// 295 - Prix du terrain
                    '""',// 296 - Prix du modèle de maison
                    '""',// 297 - Nom de l'agence gérant le terrain
                    '""',// 298 - Latitude
                    '""',// 299 - Longitude
                    '""',// 300 - Précision GPS
                    '"4.11"',// 301 - Version Format
                    '""',// 302 - Honoraires à la charge de l'acquéreur
                    '""',// 303 - Prix hors honoraires acquéreur
                    '""',// 304 - Modalités charges locataire
                    '""',// 305 - Complément loyer
                    '""',// 306 - Part honoraires état des lieux
                    '""',// 307 - URL du Barème des honoraires de l’Agence
                    '""',// 308 - Prix minimum
                    '""',// 309 - Prix maximum
                    '""',// 310 - Surface minimale
                    '""',// 311 - Surface maximale
                    '""',// 312 - Nombre de pièces minimum
                    '""',// 313 - Nombre de pièces maximum
                    '""',// 314 - Nombre de chambres minimum
                    '""',// 315 - Nombre de chambres maximum
                    '""',// 316 - ID type étage
                    '""',// 317 - Si combles aménageables
                    '""',// 318 - Si garage
                    '""',// 319 - ID type garage
                    '""',// 320 - Si possibilité mitoyenneté
                    '""',// 321 - Surface terrain nécessaire
                    '""',// 322 - Localisation
                    '""',// 323 - Nom du modèle
                    '"' . $dpeAt . '"',                                             // 324 - Date réalisation DPE
                    '""',                                                       // 325 - Version DPE
                    '"' . $property['dpeEstimateEnergyDown'] . '"',                 // 326 - DPE coût min conso
                    '"' . $property['dpeEstimateEnergyUp'] . '"',                   // 327 - DPE coût max conso
                    '"' . $RefDPE . '"',                                            // 328 - DPE date référence conso
                    '""',                                                       // 329 - Surface terrasse
                    '""',                                                       // 330 - DPE coût conso annuelle
                    '""',                                                       // 331 - Loyer de base
                    '""',                                                       // 332 - Loyer de référence majoré
                    '""',                                                       // 333 - Encadrement des loyers
                );
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
