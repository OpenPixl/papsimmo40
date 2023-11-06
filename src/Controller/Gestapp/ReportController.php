<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\PropertyEquipement;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

class ReportController extends AbstractController
{
    #[Route('/gestapp/report', name: 'app_gestapp_report')]
    public function index(): Response
    {
        return $this->render('gestapp/report/index.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }

    /**
     * Génération du Fichiers CSV pour PARUVendu
     **/
    #[Route('/report/report_properties_csv', name: 'app_gestapp_report_propertycsv')]
    public function PropertyCSV(PropertyRepository $propertyRepository, PhotoRepository $photoRepository,PropertyService $propertyService): Response
    {
        $properties = $propertyRepository->reportpropertycsv();
        //dd($properties);

        $app = $this->container->get('router')->getContext()->getHost();
        //dd($properties);

        $rows = array();
        foreach ($properties as $property) {
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $propertyService->getDestination($propriete);
            $data = str_replace(array("\n", "\r"), array('', ''), html_entity_decode($property['annonce']));
            $annonce = strip_tags($data, '<br>');

            // Contruction de la référence de l'anonnce
            $dup = $property['dup'];
            if ($dup) {
                $refProperty = $property['ref'] . $dup;
                $refMandat = $property['refMandat'] . $dup;
            } else {
                $refProperty = $property['ref'];
                $refMandat = $property['refMandat'];
            }

            if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
            } else {
                $dpeAt = "";
            }
            // Clé de détermination PARUVENDU - FAMILLE
            if ($property['projet']) {
                $famille = $property['familyCode'];
            } else {
                $famille = "";
            }
            // Clé de détermination PARUVENDU - RUBRIQUE
            if ($property['familyCode']) {
                $rubrique = $property['rubricCode'];
            } else {
                $rubrique = "00";
            }
            // Clé de détermination PARUVENDU - SSRUBRIQUE
            if ($property['rubricCode']) {
                $ssrubrique = $property['rubricssCode'];
            } else {
                $ssrubrique = "000";
            }
            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property['id']]);
            if (!$photos) {
                $url = [];
                for ($i = 1; $i < 16; $i++) {
                    ${'url' . $i} = '';
                    array_push($url, ${'url' . $i});
                }
            } else {
                $url = [];
                $arraykey = array_keys($photos);
                for ($key = 0; $key < 15; $key++) {
                    if (array_key_exists($key, $arraykey)) {
                        ${'url' . $key + 1} = 'http://' . $app . '/images/galery/' . $photos[$key]['galeryFrontName'];
                        array_push($url, ${'url' . $key + 1});
                    } else {
                        ${'url' . $key + 1} = '';
                        array_push($url, ${'url' . $key + 1});
                    }
                }
            }
            // si bien en situation de vente
            If($property['price'] > 0){
                $price = $property['price'];
                $priceFai = $property['priceFai'];
                $rent = "";
                $rentCharge = "";
                $rentWithCharge = "";
                $rentChargeModsPayment = "";
                $warrantyDeposit = "";
                $rentChargeHonoraire = "";
            }else{
                $price = "";
                $priceFai = "";
                $rent = $property['rent'];
                $rentCharge = $property['rentCharge'];
                $rentWithCharge = $rent + $rentCharge;
                $warrantyDeposit = $property['warrantyDeposit'];
                $rentChargeModsPayment = $property['rentChargeModsPayment'];
                $rentChargeHonoraire = $property['rentChargeHonoraire'];
            }
            //dd($rentWithCharge);

            // Alimentation d'une ligne du fichier CSV
            $data = array(
                '"3C14110"',                                                // 1 - code Client fournis par PV
                '"' . $refProperty . '"',                                   // 2 - Référence ANNONCE du PAPSIMMO
                '"I"',                                                      // 3 - Code Pour les biens immobiliers correspondance PV
                '"' . $famille . '"',                                       // 4 - famille Paru-Vendu
                '"' . $rubrique . '"',                                      // 5 - rubrique Paru-Vendu
                '"' . $ssrubrique . '"',                                    // 6 - sous rubrique Paru-Vendu
                '""',                                                       // 7 - code INSEE COMMUNE
                '"' . $property['zipcode'] . '"',                           // 8 - Code postal
                '"' . $property['city'] . '"',                              // 9 - Commune
                'France',                                                   // 10 - Pays
                '"' . $property['name'] . '"',                              // 11 - Titre
                '"' . $annonce . '"',                                       // 12 - Annonce
                '"' . $property['gsm'] . '"',                               // 13 - Téléphone vendeur
                '""',                                                       // 14 - Téléphone 2 vendeur - Fax
                '"' . $property['email'] . '"',                             // 15 - Email Vendeur
                '"' . $url1 . '"',                                          // 16 - Chemin de la 1ère photo
                '"' . $url2 . '"',                                          // 17 - Chemin de la 2de photo
                '"' . $url3 . '"',                                          // 18 - Chemin de la 3ème photo
                '"' . $url4 . '"',                                          // 19 - Chemin de la 4ème photo
                '"' . $url5 . '"',                                          // 20 - Chemin de la 5ème photo
                '"' . $url6 . '"',                                          // 21 - Chemin de la 6ème photo
                '"' . $priceFai . '"',                                      // 22 - Prix
                '"' . $rentWithCharge . '"',                                // 23 - Loyer Charges comprises
                '"' . $rent . '"',                                          // 24 - Loyer sans charges
                '"' . $rentCharge . '"',                                    // 25 - Charges
                '"' . $rentChargeHonoraire . '"',                           // 26 - Honoraires Charges Locataires
                '"0"',                                                      // 27 - A ajouter dans la BDD - Terrain ou bien Constructible
                '"' . $property['surfaceHome'] . '"',                       // 28
                '"' . $property['surfaceLand'] . '"',                       // 29
                '""',                                                   // 30 - Nom du Quartier
                '"' . $property['isFurnished'] . '"',                       // 31
                '"' . $property['piece'] . '"',                             // 32 - Nombre de pièces
                '""',                                                   // 33 - Url de visite virtuelle
                '""',                                                   // 34 - Texte supplémentaire
                '""',                                                   // 35 - Programme immo neuf
                '"' . $property['level'] . '"',                             // 36 - Etage
                '""',                                                   // 37 - Lien contact - Programme imm neuf
                "1",                                                    // 38 - Mettre en ligne le bien - PV
                '""',                                                   // 39 - Ancienneté
                '"' . $property['constructionAt'] . '"',                    // 40 - Année de construction
                '""',                                // 41 - Dépot de garantie
                '"' . $property['room'] . '"',                              // 42 - Nombre de chambres
                '"' . $property['bathroom'] . '"',                          // 43 - Nombre de salles de bain
                '""',                                                   // 44 - Nombre de parking extérieur
                '""',                                                   // 45 - Nombre de parking intérieur
                '"' . $property['diagDpe'] . '"',                           // 46 - DPE
                '"' . $property['diagGes'] . '"',                           // 47 - GES
                '"' . $property['isWithExclusivity'] . '"',                 // 48 - Exclusivité
                '"0"',                                                  // 49 - Honoraire à la charge de l'acquéreur
                '""',                                                   // 50 - Pourcentage de honoraires à la charge de l'acquéreur
                '"' . $property['coproperty'] . '"',                        // 51
                '""',                                                   // 52 - Nombre de lots
                "0",                                                    // 53 - Montant moyen des charges annuelles
                '""',                                                   // 54 - procédure sur le syndicat des copropriétaires
                '""',                                                   // 55 - détail sur la procédure ci dessus
                '"' . $url7 . '"',                                          // 56 - url photo 7
                '"' . $url8 . '"',                                          // 57 - url photo 8
                '"' . $url9 . '"',                                          // 58 - url photo 9
                '"' . $rentChargeModsPayment . '"',                                                   // 59 - Modalité Règlement charges - Location
                '""',                                                   // 60 - Complement de loyer
                '"' . $warrantyDeposit . '"',                                                   // 61 - Dépôt de garantie
                '""',                                                   // 62
                '"' . $property['price'] . '"',                             // 63 -
                '""',                                                   // 64 - url Baremes Honoraires
                '"' . $url10 . '"',                                         // 65 - url photo 10
                '"' . $url11 . '"',                                         // 66 - url photo 11
                '"' . $url12 . '"',                                         // 67 - url photo 12
                '"' . $url13 . '"',                                         // 68 - url photo 13
                '"' . $url14 . '"',                                         // 69 - url photo 14
                '"' . $url15 . '"',                                         // 70 - url photo 15
                '"' . $dpeAt . '"',                                         // 71
                '"' . $property['dpeEstimateEnergyDown'] . '"',             // 72
                '"' . $property['dpeEstimateEnergyUp'] . '"',               // 73
            );
            $rows[] = implode('|', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

    /**
     * Génération du Fichiers CSV pour MeilleurAgent/LeBonCoin
     **/
    #[Route('/report/report_properties_csv2', name: 'app_gestapp_report_propertycsv2')]
    public function PropertyCSV2(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
        PropertyService $propertyService,
    ): Response
    {
        $properties = $propertyRepository->reportpropertycsv2();
        //dd($properties);

        $app = $this->container->get('router')->getContext()->getHost();
        //dd($properties);

        $rows = array();
        foreach ($properties as $property) {
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $propertyService->getDestination($propriete);
            // Description de l'annonce
            $data = str_replace(array("\n", "\r"), array('', ''), html_entity_decode($property['annonce']));
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Récupération de la reference
            $refs = $propertyService->getRefs($propriete);

            // Sélection du type de bien
            $propertyDefinition = $property['propertyDefinition'];
            if ($propertyDefinition == 'Propriété / Château') {
                $bien = 'Château';
            } elseif ($propertyDefinition == 'A définir') {
                $bien = 'Inconnu';
            } elseif ($propertyDefinition == 'Atelier') {
                $bien = 'loft/atelier/surface';
            } elseif ($propertyDefinition == 'Parking / Garage') {
                $bien = 'Parking/box';
            } else {
                $bien = $propertyDefinition;
            }

            // Préparation de la date dpeAt
            if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
            } else {
                $dpeAt = "";
            }

            // Préparation de la date de création mandat
            if ($property['mandatAt'] && $property['mandatAt'] instanceof \DateTime) {
                $mandatAt = $property['mandatAt']->format('d/m/Y');
            } else {
                $mandatAt = "";
            }

            // Préparation de la date de création RefDPE
            if ($property['RefDPE'] && $property['RefDPE'] instanceof \DateTime) {
                $RefDPE = $property['RefDPE']->format('d/m/Y');
            } else {
                $RefDPE = "";
            }

            // Calcul des honoraires en %
            // $honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property['id']]);
            if (!$photos) {
                $url = [];
                $titrephoto = [];
                for ($i = 1; $i < 31; $i++) {
                    ${'url' . $i} = '';
                    array_push($url, ${'url' . $i});
                }
                // génération des titres de photos
                for ($i = 1; $i < 31; $i++) {
                    ${'titrephoto' . $i} = '';
                    array_push($titrephoto, ${'titrephoto' . $i});
                }
            } else {
                $url = [];
                $arraykey = array_keys($photos);
                for ($key = 0; $key < 30; $key++) {
                    if (array_key_exists($key, $arraykey)) {
                        ${'url' . $key + 1} = 'http://' . $app . '/images/galery/' . $photos[$key]['galeryFrontName'] . "?" . $photos[$key]['createdAt']->format('Ymd');
                        array_push($url, ${'url' . $key + 1});
                    } else {
                        ${'url' . $key + 1} = '';
                        array_push($url, ${'url' . $key + 1});
                    }
                }
                // génération des titres de photos
                for ($key = 0; $key < 30; $key++) {
                    if (array_key_exists($key, $arraykey)) {
                        ${'titrephoto' . $key + 1} = 'Photo-' . $property['ref'] . '-' . $key + 1;
                        array_push($url, ${'titrephoto' . $key + 1});
                    } else {
                        ${'titrephoto' . $key + 1} = '';
                        array_push($url, ${'titrephoto' . $key + 1});
                    }
                }
            }

            // Orientation
            $orientation = $property['orientation'];
            if ($orientation = 'nord') {
                $nord = 1;
                $est = 0;
                $sud = 0;
                $ouest = 0;
            } elseif ($orientation = 'est') {
                $nord = 0;
                $est = 1;
                $sud = 0;
                $ouest = 0;
            } elseif ($orientation = 'sud') {
                $nord = 0;
                $est = 0;
                $sud = 1;
                $ouest = 0;
            } else {
                $nord = 0;
                $est = 0;
                $sud = 0;
                $ouest = 1;
            }

            // publication sur les réseaux
            $publications = [];
            if ($property['seloger'] == 1) {
                array_push($publications, 'MEILLEURSAGENTS');
            }
            if ($property['leboncoin'] == 1) {
                array_push($publications, 'LEBONCOIN_IMMO_V2');
            }
            $listpublications = implode(",", $publications);

            // Transformation terrace en booléen
            if ($property['terrace']) {
                $terrace = 1;
            } else {
                $terrace = 0;
            }

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id' => $idcomplement]);
            //dd($equipments);

            // Récupération DPE & GES
            $bilanDpe = $propertyService->getClasseDpe($propriete);
            $bilanGes = $propertyService->getClasseGes($propriete);

            // Création d'une ligne du tableau
            $data = array(
                '"papsimmo"',                                                   // 1 - Identifiant Agence
                '"' . $refs['ref'] . '"',                                       // 2 - Référence agence du bien
                '"' . $destination['destination'] . '"',                        // 3 - Type d’annonce
                '"' . $destination['typeBien'] . '"',                           // 4 - Type de bien
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
                '"' . $destination['rentChargeHonoraire'] . '"',                // 15 - Honoraires                                                 // 15 - Honoraires
                '"' . $property['surfaceHome'] . '"',                           // 16 - Surface (m²)
                '"' . $property['surfaceLand'] . '"',                           // 17 - Surface terrain (m²)
                '"' . $property['piece'] . '"',                                 // 18 - NB de pièces
                '"' . $property['room'] . '"',                                  // 19 - NB de chambres
                '"' . $property['name'] . '"',                                  // 20 - Libellé
                '"' . $annonce . '"',                                           // 21 - Descriptif
                '"' . $property['disponibilityAt'] . '"',                       // 22 - Date de disponibilité
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
                '"0"',// 41 - Ascenseur
                '"0"',// 42 - Cave
                '""',                                                           // 43 - NB de parkings
                '"0"',                                                          // 44 - NB de boxes
                '"0"',// 45 - Digicode
                '"0"',// 46 - Interphone
                '"0"',// 47 - Gardien
                '"' . $terrace . '"',                                           // 48 - Terrasse
                '""',                                                           // 49 - Prix semaine Basse Saison
                '""',                                                           // 50 - Prix quinzaine Basse Saison
                '""',                                                           // 51 - Prix mois / Basse Saison
                '""',                                                           // 52 - Prix semaine Haute Saison
                '""',                                                           // 53 - Prix quinzaine Haute Saison
                '""',                                                           // 54 - Prix mois Haute Saison
                '""',                                                           // 55 - NB de personnes
                '""',                                                           // 56 - Type de résidence
                '""',                                                           // 57 - Situation
                '""',                                                           // 58 - NB de couverts
                '""',                                                           // 59 - NB de lits doubles
                '""',                                                           // 60 - NB de lits simples
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
                '"' . $listpublications . '"',                                  // 82 - Publications
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
                '"' . $refs['refMandat'] . '"',                             // 112 - N° de mandat
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

        //dd($rows);

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        //dd($response);

        return $response;
    }

    /**
     * Génération du Fichiers XML pour green-acres
     **/
    #[Route('/report/annoncesGreenAcres', name: 'app_gestapp_report_annoncesgreenacres')]
    public function PropertyGreenAcres(
        PropertyRepository   $propertyRepository,
        PhotoRepository      $photoRepository,
        ComplementRepository $complementRepository,
        PropertyEquipementRepository $propertyEquipementRepository

    ): Response
    {
        // PARTIE I : Génération du fichier CSV

        $properties = $propertyRepository->reportpropertyGreenacresFTP();            // On récupère les biens à publier sur SeLoger
        $app = $this->container->get('router')->getContext()->getHost();    // On récupère l'url de l'appl pour les url des photos

        $adverts = [];                                                    // Construction du tableau
        foreach ($properties as $property) {
            $property = $propertyRepository->find($property['id']);
            //dd($property);

            // Equipement
            $options = $property->getOptions();
            $equipment = $options->getPropertyOtheroption();

            // Publication
            $publication = $property->getPublication();

            //rubric
            $rubric = $property->getRubric();

            // Description de l'annonce
            $data = str_replace(array("\n", "\r"), array('', ''), html_entity_decode($property->getAnnonce()));
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Contruction de la référence de l'anonnce
            $dup = $property->getDupMandat();
            if ($dup) {
                $refProperty = $property->getRef() . $dup;
                $refMandat = $property->getRefMandat() . $dup;
            } else {
                $refProperty = $property->getRef();
                $refMandat = $property->getRefMandat();
            }

            // Préparation de la date dpeAt
            if ($property->getDpeAt() && $property->getDpeAt() instanceof \DateTime) {
                $dpeAt = $property->getDpeAt()->format('d/m/Y');
            } else {
                $dpeAt = "";
            }

            // Préparation de la date de réation mandat
            if ($property->getMandatAt() && $property->getMandatAt() instanceof \DateTime) {
                $mandatAt = $property->getMandatAt()->format('d/m/Y');
            } else {
                $mandatAt = "";
            }

            // Préparation de la date de création RefDPE
            if ($property->getEeaYear() && $property->getEeaYear() instanceof \DateTime) {
                $RefDPE = $property->getEeaYear()->format('d/m/Y');
            } else {
                $RefDPE = "";
            }

            // Calcul des honoraires en %
            $honoraires = round(100 - (($property->getPrice() * 100) / $property->getPriceFai()), 2);

            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property->getId()]);

            $pics = [];
            if (!$photos) {                                                                       // Si aucune photo présente
                $pics = [];
            } else {
                foreach($photos as $photo)
                {
                    $urlphoto = 'http://' . $app . '/images/galery/' . $photo['galeryFrontName'];
                    $titrephoto = 'Photo-' . $property->getRef() . '-' . + 1;
                    $pic = [
                        'urlphoto' => $urlphoto,
                        'titrephoto' => $titrephoto,
                    ];
                    array_push($pics, $pic);
                }

            }

            // Orientation
            $orientation = $options->getPropertyOrientation();
            //dd($orientation);
            if ($orientation = 'nord') {
                $nord = 1;
                $est = 0;
                $sud = 0;
                $ouest = 0;
            } elseif ($orientation = 'est') {
                $nord = 0;
                $est = 1;
                $sud = 0;
                $ouest = 0;
            } elseif ($orientation = 'sud') {
                $nord = 0;
                $est = 0;
                $sud = 1;
                $ouest = 0;
            } else {
                $nord = 0;
                $est = 0;
                $sud = 0;
                $ouest = 1;
            }

            // publication sur les réseaux
            $publications = 'SL';

            // Transformation terrace en booléen
            if ($options->getTerrace()) {
                $terrace = 1;
            } else {
                $terrace = 0;
            }

            // BILAN DPE
            if ($property->getDiagDpe() > 0 and $property->getDiagDpe() <= 70) {
                $bilanDpe = 'A';
            } elseif ($property->getDiagDpe() > 70 and $property->getDiagDpe() <= 110) {
                $bilanDpe = 'B';
            } elseif ($property->getDiagDpe() > 110 and $property->getDiagDpe() <= 180) {
                $bilanDpe = 'C';
            } elseif ($property->getDiagDpe() > 180 and $property->getDiagDpe() <= 250) {
                $bilanDpe = 'D';
            } elseif ($property->getDiagDpe() > 250 and $property->getDiagDpe() <= 330) {
                $bilanDpe = 'E';
            } elseif ($property->getDiagDpe() > 330 and $property->getDiagDpe() <= 420) {
                $bilanDpe = 'F';
            } else {
                $bilanDpe = 'G';
            }

            // Bilan GES
            if ($property->getDiagGes() > 0 and $property->getDiagGes() <= 6) {
                $bilanGes = 'A';
            } elseif ($property->getDiagGes() > 6 and $property->getDiagGes() <= 11) {
                $bilanGes = 'B';
            } elseif ($property->getDiagGes() > 11 and $property->getDiagGes() <= 30) {
                $bilanGes = 'C';
            } elseif ($property->getDiagGes() > 30 and $property->getDiagGes() <= 50) {
                $bilanGes = 'D';
            } elseif ($property->getDiagGes() > 50 and $property->getDiagGes() <= 70) {
                $bilanGes = 'E';
            } elseif ($property->getDiagGes() > 70 and $property->getDiagGes() <= 100) {
                $bilanGes = 'F';
            } else {
                $bilanGes = 'G';
            }

            if ($property->getDiagChoice() == "obligatoire") {
                $diagDPEChoice = "D";
                $diagGESChoice = "E";
            } elseif ($property->getDiagChoice() == "vierge") {
                $diagDPEChoice = "VI";
                $diagGESChoice = "VI";
            } else {
                $diagDPEChoice = "NS";
                $diagGESChoice = "NS";
            }

            //dd($property['rubric_en']);

            $xml = [
                'equipments' => $equipment ,
                'reference' => $property->getRef(),
                'accountReference' => '892318a',
                'title' => $property->getName(),
                'price' => $property->getPriceFai(),
                'fees' => $property->getHonoraires(),
                'pictureNumber' => count($photos),
                'department' => substr($property->getZipcode(),0,2),
                'city' => $property->getCity(),
                'postalCode' => $property->getZipcode(),
                'country' => 'fr',
                'status' => $publication->isIsPublishgreenacres(),
                'annonce' => $annonce,
                'type' => $rubric->getEn(),
                'surfaceLand' => $property->getSurfaceLand(),
                'surfaceHome' => $property->getSurfaceHome(),
                'rooms' => $property->getPiece(),
                'bedrooms' => $property->getRoom(),
                'dpe' => $bilanDpe,
                'dpe_value' => $property->getDiagDpe(),
                'ges' => $bilanGes,
                'ges_value' => $property->getDiagGes(),
                'bathroom' => $options->getBathroom(),
                'washroom' => $options->getWashroom(),
                'wc' => $options->getWc(),
                'terrace' => $options->getTerrace(),
                'balcony' => $options->getBalcony(),
                'level' => $options->getLevel(),
                'isFurnished' => $options->getIsFurnished(),
                'heating' => $options->getPropertyEnergy(),
                'charge' => $property->getRentCharge(),
                'pics' => $pics
            ];
            array_push($adverts, $xml);

        }
        //dd($adverts);
        $xmlContent = $this->renderView('gestapp/report/greenacrees.html.twig', [
            'adverts' => $adverts
        ]);


        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/AnnoncesGreen/annonces.xml';                                  // Chemin du fichier
        if (file_exists($file)) {
            unlink($file);                                                  // Suppression du précédent s'il exist
            file_put_contents('doc/report/AnnoncesGreen/Annonces.xml', $xmlContent); // Génération du fichier dans l'arborescence du fichiers du site
        }
        file_put_contents('doc/report/AnnoncesGreen/Annonces.xml', $xmlContent);     // Génération du fichier dans l'arborescence du fichiers du site

        // return response in XML format
        $response = new Response($xmlContent);
        $response->headers->set('Content-type', 'text/xml');

        return $response;

    }
}