<?php

namespace App\Controller\Gestapp;

use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
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
    public function PropertyCSV(PropertyRepository $propertyRepository, PhotoRepository $photoRepository): Response
    {
        $properties = $propertyRepository->reportpropertycsv();

        $app = $this->container->get('router')->getContext()->getHost();
        //dd($properties);

        $rows = array();
        foreach ($properties as $property){

            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');

            // Contruction de la référence de l'anonnce
            $dup = $property['dup'];
            if($dup){
                $refProperty = $property['ref'].$dup;
                $refMandat = $property['refMandat'].$dup;
            }else{
                $refProperty = $property['ref'];
                $refMandat = $property['refMandat'];
            }

            if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
            }else{
                $dpeAt ="";
            }
            // Clé de détermination PARUVENDU - FAMILLE
            if($property['projet']){
                $famille = $property['familyCode'];
            }else{
                $famille = "";
            }
            // Clé de détermination PARUVENDU - RUBRIQUE
            if($property['propertyDefinition']){
                $rubrique = $property['rubricCode'];
            }else{
                $rubrique = "00";
            }
            // Clé de détermination PARUVENDU - SSRUBRIQUE
            if($property['ssCategory']){
                $ssrubrique = $property['rubricssCode'];
            }else{
                $ssrubrique = "000";
            }
            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property['id']]);
            if(!$photos){
                $url = [];
                for ($i = 1; $i<16; $i++){
                    ${'url'.$i} = '';
                    array_push($url, ${'url'.$i});
                }
            }else{
                $url = [];
                $arraykey = array_keys($photos);
                for ($key = 0; $key<15; $key++){
                    if(array_key_exists($key,$arraykey)){
                        ${'url'.$key+1} = 'http://'.$app.'/images/galery/'.$photos[$key]['galeryFrontName'];
                        array_push($url, ${'url'.$key+1});
                    }else{
                        ${'url'.$key+1} = '';
                        array_push($url, ${'url'.$key+1});
                    }
                }
            }

            // Alimentation d'une ligne du fichier CSV
            $data = array(
                '"3C14110"',                                            // 1 - code Client fournis par PV
                '"'.$refProperty.'"',                                   // 2 - Référence ANNONCE du PAPSIMMO
                '"I"',                                                  // 3 - Code Pour les biens immobiliers correspondance PV
                '"'.$famille.'"',                                       // 4 - famille Paru-Vendu
                '"'.$rubrique.'"',                                      // 5 - rubrique Paru-Vendu
                '"'.$ssrubrique.'"',                                    // 6 - sous rubrique Paru-Vendu
                '""',                                                   // 7 - code INSEE COMMUNE
                '"'.$property['zipcode'].'"',                           // 8 - Code postal
                '"'.$property['city'].'"',                              // 9 - Commune
                'France',                                               // 10 - Pays
                '"'.$property['name'].'"',                              // 11 - Titre
                '"'.$annonce.'"',                                       // 12 - Annonce
                '"'.$property['gsm'].'"',                               // 13 - Téléphone vendeur
                '""',                                                   // 14 - Téléphone 2 vendeur - Fax
                '"'.$property['email'].'"',                             // 15 - Email Vendeur
                '"'.$url1.'"',                                          // 16 - Chemin de la 1ère photo
                '"'.$url2.'"',                                          // 17 - Chemin de la 2de photo
                '"'.$url3.'"',                                          // 18 - Chemin de la 3ème photo
                '"'.$url4.'"',                                          // 19 - Chemin de la 4ème photo
                '"'.$url5.'"',                                          // 20 - Chemin de la 5ème photo
                '"'.$url6.'"',                                          // 21 - Chemin de la 6ème photo
                '"'.$property['priceFai'].'"',                          // 22 - Prix
                '"0"',                                                  // 23 - Loyer Charges comprises
                '"0"',                                                  // 24 - Loyer sans charges
                '"0"',                                                  // 25 - Charges
                '"0"',                                                  // 26 - Honoraires Charges Locataires
                '"0"',                                                  // 27 - A ajouter dans la BDD - Terrain ou bien Constructible
                '"'.$property['surfaceHome'].'"',                       // 28
                '"'.$property['surfaceLand'].'"',                       // 29
                '""',                                                   // 30 - Nom du Quartier
                '"'.$property['isFurnished'].'"',                       // 31
                '"'.$property['piece'].'"',                             // 32 - Nombre de pièces
                '""',                                                   // 33 - Url de visite virtuelle
                '""',                                                   // 34 - Texte supplémentaire
                '""',                                                   // 35 - Programme immo neuf
                '"'.$property['level'].'"',                             // 36 - Etage
                '""',                                                   // 37 - Lien contact - Programme imm neuf
                "1",                                                    // 38 - Mettre en ligne le bien - PV
                '""',                                                   // 39 - Ancienneté
                '"'.$property['constructionAt'].'"',                    // 40 - Année de construction
                '""',                                                   // 41 - Dépot de garantie
                '"'.$property['room'].'"',                              // 42 - Nombre de chambres
                '"'.$property['bathroom'].'"',                          // 43 - Nombre de salles de bain
                '""',                                                   // 44 - Nombre de parking extérieur
                '""',                                                   // 45 - Nombre de parking intérieur
                '"'.$property['diagDpe'].'"',                           // 46 - DPE
                '"'.$property['diagGes'].'"',                           // 47 - GES
                '"'.$property['isWithExclusivity'].'"',                 // 48 - Exclusivité
                '"0"',                                                  // 49 - Honoraire à la charge de l'acquéreur
                '""',                                                   // 50 - Pourcentage de honoraires à la charge de l'acquéreur
                '"'.$property['coproperty'].'"',                        // 51
                '""',                                                   // 52 - Nombre de lots
                "0",                                                    // 53 - Montant moyen des charges annuelles
                '""',                                                   // 54 - procédure sur le syndicat des copropriétaires
                '""',                                                   // 55 - détail sur la procédure ci dessus
                '"'.$url7.'"',                                          // 56 - url photo 7
                '"'.$url8.'"',                                          // 57 - url photo 8
                '"'.$url9.'"',                                          // 58 - url photo 9
                '""',                                                   // 59 - Modalité Règlement charges - Location
                '""',                                                   // 60 - Complement de loyer
                '""',                                                   // 61 - Dépôt de garantie
                '""',                                                   // 62
                '"'.$property['price'].'"',                             // 63 -
                '""',                                                   // 64 - url Baremes Honoraires
                '"'.$url10.'"',                                         // 65 - url photo 10
                '"'.$url11.'"',                                         // 66 - url photo 11
                '"'.$url12.'"',                                         // 67 - url photo 12
                '"'.$url13.'"',                                         // 68 - url photo 13
                '"'.$url14.'"',                                         // 69 - url photo 14
                '"'.$url15.'"',                                         // 70 - url photo 15
                '"'.$dpeAt.'"',                                         // 71
                '"'.$property['dpeEstimateEnergyDown'].'"',             // 72
                '"'.$property['dpeEstimateEnergyUp'].'"',               // 73
            );
            $rows[] = implode('|', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

    /**
     * Génération du Fichiers CSV pour MeilleurAgent
     **/
    #[Route('/report/report_properties_csv2', name: 'app_gestapp_report_propertycsv2')]
    public function PropertyCSV2(PropertyRepository $propertyRepository, PhotoRepository $photoRepository, ComplementRepository $complementRepository): Response
    {
        $properties = $propertyRepository->reportpropertycsv2();

        //dd($properties);

        $app = $this->container->get('router')->getContext()->getHost();
        //dd($properties);

        $rows = array();
        foreach ($properties as $property){
            // Description de l'annonce
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Contruction de la référence de l'anonnce
            $dup = $property['dup'];
            if($dup){
                $refProperty = $property['ref'].$dup;
                $refMandat = $property['refMandat'].$dup;
            }else{
                $refProperty = $property['ref'];
                $refMandat = $property['refMandat'];
            }

            // Sélection du type de bien
            $propertyDefinition = $property['propertyDefinition'];
            if($propertyDefinition == 'Propriété / Château'){
                $bien = 'Château';
            }elseif($propertyDefinition == 'A définir'){
                $bien = 'Inconnu';
            }elseif($propertyDefinition == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }
            elseif($propertyDefinition == 'Parking / Garage'){
                $bien = 'Parking/box';
            }else{
                $bien = $propertyDefinition;
            }

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

            // Calcul des honoraires en %
            $honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property['id']]);
            if(!$photos){
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
                        ${'url'.$key+1} = 'http://'.$app.'/images/galery/'.$photos[$key]['galeryFrontName']."?".$photos[$key]['createdAt']->format('Ymd');
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
            $publications = [];
            if ($property['seloger'] == 1){
                array_push($publications,'MEILLEURSAGENTS');
            }
            if ($property['leboncoin'] == 1){
                array_push($publications,'LEBONCOIN_IMMO_V2');
            }
            $listpublications = implode(",",$publications);

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

            // BILAN DPE
            if($property['diagDpe'] > 0 and $property['diagDpe'] <= 50 ){
                $bilanDpe = 'A';
            }elseif($property['diagDpe'] > 50 and $property['diagDpe'] <= 90 ){
                $bilanDpe = 'B';
            }elseif($property['diagDpe'] > 90 and $property['diagDpe'] <= 150 ){
                $bilanDpe = 'C';
            }elseif($property['diagDpe'] > 150 and $property['diagDpe'] <= 230 ){
                $bilanDpe = 'D';
            }elseif($property['diagDpe'] > 230 and $property['diagDpe'] <= 330 ){
                $bilanDpe = 'E';
            }elseif($property['diagDpe'] > 330 and $property['diagDpe'] <= 450 ){
                $bilanDpe = 'F';
            }else{
                $bilanDpe = 'G';
            }

            // Bilan GES
            if($property['diagGes'] > 0 and $property['diagGes'] <= 50 ){
                $bilanGes = 'A';
            }elseif($property['diagGes'] > 50 and $property['diagGes'] <= 90 ){
                $bilanGes = 'B';
            }elseif($property['diagGes'] > 90 and $property['diagGes'] <= 150 ){
                $bilanGes = 'C';
            }elseif($property['diagGes'] > 150 and $property['diagGes'] <= 230 ){
                $bilanGes = 'D';
            }elseif($property['diagGes'] > 230 and $property['diagGes'] <= 330 ){
                $bilanGes = 'E';
            }elseif($property['diagGes'] > 330 and $property['diagGes'] <= 450 ){
                $bilanGes = 'F';
            }else{
                $bilanGes = 'G';
            }

            if($property['diagChoice'] == "obligatoire"){
                $diagDPEChoice = "D";
                $diagGESChoice = "E";
            }elseif($property['diagChoice'] == "vierge"){
                $diagDPEChoice = "VI";
                $diagGESChoice = "VI";
            }else{
                $diagDPEChoice = "NS";
                $diagGESChoice = "NS";
            }

            // Création d'une ligne du tableau
            $data = array(
                '"papsimmo"',                                               // 1 - Identifiant Agence
                '"'.$refProperty.'"',                                   // 2 - Référence agence du bien
                '"Vente"',                                                  // 3 - Type d’annonce
                '"'.$bien.'"',                                              // 4 - Type de bien
                '"'.$property['zipcode'].'"',                               // 5 - CP
                '"'.$property['city'].'"',                                  // 6 - Ville
                '"France"',                                                 // 7 - Pays
                '"'.$property['adress'].'"',                                // 8 - Adresse
                '""',                                                       // 9 - Quartier / Proximité
                '""',                                                       // 10 - Activités commerciales
                '"'.$property['priceFai'].'"',                              // 11 - Prix / Loyer / Prix de cession
                '""',                                                       // 12 - Loyer / mois murs
                '"0"',                                                      // 13 - Loyer CC
                '"0"',                                                      // 14 - Loyer HT
                '""',                                                       // 15 - Honoraires
                '"'.$property['surfaceHome'].'"',                           // 16 - Surface (m²)
                '"'.$property['surfaceLand'].'"',                           // 17 - Surface terrain (m²)
                '"'.$property['piece'].'"',                                 // 18 - NB de pièces
                '"'.$property['room'].'"',                                  // 19 - NB de chambres
                '"'.$property['name'].'"',                                  // 20 - Libellé
                '"'.$annonce.'"',                                           // 21 - Descriptif
                '"'.$property['disponibilityAt'].'"',                       // 22 - Date de disponibilité
                '""',                                                       // 23 - Charges
                '"'.$property['level'].'"',                                 // 24 - Etage
                '""',                                                       // 25 - NB d’étages
                '"'.$property['isFurnished'].'"',                           // 26 - Meublé
                '"'.$property['constructionAt'].'"',                        // 27 - Année de construction
                '""',                                                       // 28 - Refait à neuf
                '"'.$property['bathroom'].'"',                              // 29 - NB de salles de bain
                '"'.$property['sanitation'].'"',                            // 30 - NB de salles d’eau
                '"'.$property['wc'].'"',                                    // 31 - NB de WC
                '"0"',                                                      // 32 - WC séparés
                '"'.$property['slCode'].'"',                                // 33 - Type de chauffage
                '""',                                                       // 34 - Type de cuisine
                '"'.$sud.'"',                                               // 35 - Orientation sud
                '"'.$est.'"',                                               // 36 - Orientation est
                '"'.$ouest.'"',                                             // 37 - Orientation ouest
                '"'.$nord.'"',                                              // 38 - Orientation nord
                '"'.$property['balcony'].'"',                               // 39 - NB balcons
                '""',                                                       // 40 - SF Balcon
                '"0"',// 41 - Ascenseur
                '"0"',// 42 - Cave
                '""',                                                       // 43 - NB de parkings
                '"0"',                                                      // 44 - NB de boxes
                '"0"',// 45 - Digicode
                '"0"',// 46 - Interphone
                '"0"',// 47 - Gardien
                '"'.$terrace.'"',                                           // 48 - Terrasse
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
                '"'.$listpublications.'"',                                  // 82 - Publications
                '"0"',                                      // 83 - Mandat en exclusivité
                '"0"',                                      // 84 - Coup de cœur
                '"'.$url1.'"',                                              // 85 - Photo 1
                '"'.$url2.'"',                                              // 86 - Photo 2
                '"'.$url3.'"',                                              // 87 - Photo 3
                '"'.$url4.'"',                                              // 88 - Photo 4
                '"'.$url5.'"',                                              // 89 - Photo 5
                '"'.$url6.'"',                                              // 90 - Photo 6
                '"'.$url7.'"',                                              // 91 - Photo 7
                '"'.$url8.'"',                                              // 92 - Photo 8
                '"'.$url9.'"',                                              // 93 - Photo 9
                '"'.$titrephoto1.'"',                                       // 94 - Titre photo 1
                '"'.$titrephoto2.'"',                                       // 95 - Titre photo 2
                '"'.$titrephoto3.'"',                                       // 96 - Titre photo 3
                '"'.$titrephoto4.'"',                                       // 97 - Titre photo 4
                '"'.$titrephoto5.'"',                                       // 98 - Titre photo 5
                '"'.$titrephoto6.'"',                                       // 99 - Titre photo 6
                '"'.$titrephoto7.'"',                                       // 100 - Titre photo 7
                '"'.$titrephoto8.'"',                                       // 101 - Titre photo 8
                '"'.$titrephoto9.'"',                                       // 102 - Titre photo 9
                '""',                                                       // 103 - Photo panoramique
                '""',                                                       // 104 - URL visite virtuelle
                '"'.$property['gsm'].'"',                                   // 105 - Téléphone à afficher
                '"'.$property['firstName'].' '.$property['lastName'].'"',   // 106 - Contact à afficher
                '"'.$property['email'].'"',                                 // 107 - Email de contact
                '"'.$property['zipcode'].'"',                               // 108 - CP Réel du bien
                '"'.$property['city'].'"',                                  // 109 - Ville réelle du bien
                '""',                                                       // 110 - Inter-cabinet
                '""',                                                       // 111 - Inter-cabinet prive
                '"'.$refMandat.'"',                             // 112 - N° de mandat
                '"'.$mandatAt.'"',                                          // 113 - Date mandat
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
                '"'.$url10.'"',                                             // 164 - Photo 10
                '"'.$url11.'"',                                             // 165 - Photo 11
                '"'.$url12.'"',                                             // 166 - Photo 12
                '"'.$url13.'"',                                             // 167 - Photo 13
                '"'.$url14.'"',                                             // 168 - Photo 14
                '"'.$url15.'"',                                             // 169 - Photo 15
                '"'.$url16.'"',                                             // 170 - Photo 16
                '"'.$url17.'"',                                             // 171 - Photo 17
                '"'.$url18.'"',                                             // 172 - Photo 18
                '"'.$url19.'"',                                             // 173 - Photo 19
                '"'.$url20.'"',                                             // 174 - Photo 20
                '""',                                                       // 175 - Identifiant technique
                '"'.$property['diagDpe'].'"',                               // 176 - Consommation énergie
                '"'.$diagDPEChoice.'"',                                     // 177 - Bilan consommation énergie
                '"'.$property['diagGes'].'"',                               // 178 - Emissions GES
                '"'.$diagGESChoice.'"',                                     // 179 - Bilan émission GES
                '""',                                                       // 180 - Identifiant quartier (obsolète)
                '"'.$property['ssCategory'].'"',                            // 181 - Sous type de bien
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
                '""',                                   // 193 - Transport : Ligne
                '""',                                   // 194 - Transport : Station
                '""',                                   // 195 - Durée bail
                '""',                                   // 196 - Places en salle
                '""',                                   // 197 - Monte-charge
                '""',                                   // 198 - Quai
                '""',                                   // 199 - Nombre de bureaux
                '""',                                   // 200 - Prix du droit d’entrée
                '""',                                   // 201 - Prix masqué
                '""',                                   // 202 - Loyer annuel global
                '""',                                   // 203 - Charges annuelles globales
                '""',                                   // 204 - Loyer annuel au m2
                '""',                                   // 205 - Charges annuelles au m2
                '"0"',                                  // 206 - Charges mensuelles  Loyer annuel CC HT
                '"0"',                                  // 207 - Loyer annuel CC
                '"0"',                                  // 208 - Loyer annuel HT
                '"0"',                                  // 209 - Charges annuelles HT
                '"0"',                                  // 210 - Loyer annuel au m2 CC
                '"0"',                                  // 211 - Loyer annuel au m2 HT
                '"0"',                                  // 212 - Charges annuelles au m2 HT
                '"0"',                                  // 213 - Divisible
                '""',                                   // 214 - Surface divisible minimale
                '""',                                   // 215 - Surface divisible maximale
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
                '"'.$property['copro'].'"',                                 // 258 - En copropriété
                '""',                                   // 259 - Nombre de lots
                '"'.$property['chargeCopro'].'"',                           // 260 - Charges annuelles
                '""',                                   // 261 - Syndicat des copropriétaires en procédure
                '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
                '""',                                   // 263 - Champ personnalisé 26
                '"'.$url21.'"',                                             // 264 - Photo 21
                '"'.$url22.'"',                                             // 265 - Photo 22
                '"'.$url23.'"',                                             // 266 - Photo 23
                '"'.$url24.'"',                                             // 267 - Photo 24
                '"'.$url25.'"',                                             // 268 - Photo 25
                '"'.$url26.'"',                                             // 269 - Photo 26
                '"'.$url27.'"',                                             // 270 - Photo 27
                '"'.$url28.'"',                                             // 271 - Photo 28
                '"'.$url29.'"',                                             // 272 - Photo 29
                '"'.$url30.'"',                                             // 273 - Photo 30
                '"'.$titrephoto10.'"',                                      // 274 - Titre photo 10
                '"'.$titrephoto11.'"',                                      // 275 - Titre photo 11
                '"'.$titrephoto12.'"',                                      // 276 - Titre photo 12
                '"'.$titrephoto13.'"',                                      // 277 - Titre photo 13
                '"'.$titrephoto14.'"',                                      // 278 - Titre photo 14
                '"'.$titrephoto15.'"',                                      // 279 - Titre photo 15
                '"'.$titrephoto16.'"',                                      // 280 - Titre photo 16
                '"'.$titrephoto17.'"',                                      // 281 - Titre photo 17
                '"'.$titrephoto18.'"',                                      // 282 - Titre photo 18
                '"'.$titrephoto19.'"',                                      // 283 - Titre photo 19
                '"'.$titrephoto20.'"',                                      // 284 - Titre photo 20
                '"'.$titrephoto21.'"',                                      // 285 - Titre photo 21
                '"'.$titrephoto22.'"',                                      // 286 - Titre photo 22
                '"'.$titrephoto23.'"',                                      // 287 - Titre photo 23
                '"'.$titrephoto24.'"',                                      // 288 - Titre photo 24
                '"'.$titrephoto25.'"',                                      // 289 - Titre photo 25
                '"'.$titrephoto26.'"',                                      // 290 - Titre photo 26
                '"'.$titrephoto27.'"',                                      // 291 - Titre photo 27
                '"'.$titrephoto28.'"',                                      // 292 - Titre photo 28
                '"'.$titrephoto29.'"',                                      // 293 - Titre photo 29
                '"'.$titrephoto30.'"',                                      // 294 - Titre photo 30
                '""',// 295 - Prix du terrain
                '""',// 296 - Prix du modèle de maison
                '""',// 297 - Nom de l'agence gérant le terrain
                '""',// 298 - Latitude
                '""',// 299 - Longitude
                '""',// 300 - Précision GPS
                '""',// 301 - Version Format
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
                '"'.$dpeAt.'"',                                             // 324 - Date réalisation DPE
                '""',                                                       // 325 - Version DPE
                '"'.$property['dpeEstimateEnergyDown'].'"',                 // 326 - DPE coût min conso
                '"'.$property['dpeEstimateEnergyUp'].'"',                   // 327 - DPE coût max conso
                '"'.$RefDPE.'"',                                // 328 - DPE date référence conso
                '""',                                                       // 329 - Surface terrasse
                '""',                                                       // 330 - DPE coût conso annuelle
                '""',                                                       // 331 - Loyer de base
                '""',                                                       // 332 - Loyer de référence majoré
                '""',                                                       // 333 - Encadrement des loyers
            );
            $rows[] = implode('!#', $data);
        }

        $content = implode("\n", $rows);

        //dd($content);

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        //dd($response);

        return $response;
    }

    /**
     * Génération du Fichiers CSV pour SeLoger
     **/
    #[Route('/report/annonces', name: 'app_gestapp_report_annonces')]
    public function PropertyCSV3(PropertyRepository $propertyRepository, PhotoRepository $photoRepository, ComplementRepository $complementRepository): Response
    {
        // PARTIE I : Génération du fichier CSV
        $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger
        $app = $this->container->get('router')->getContext()->getHost();    // On récupère l'url de l'appl pour les url des photos

        $rows = array();                                                    // Construction du tableau
        foreach ($properties as $property){
            // Description de l'annonce
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Contruction de la référence de l'anonnce
            $dup = $property['dup'];
            if($dup){
                $refProperty = $property['ref'].$dup;
                $refMandat = $property['refMandat'].$dup;
            }else{
                $refProperty = $property['ref'];
                $refMandat = $property['refMandat'];
            }

            // Sélection du type de bien
            $propertyDefinition = $property['propertyDefinition'];
            if($propertyDefinition == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($propertyDefinition == 'Vente'){                                    // A CORRIGER D'URGENCE POUR LE BON FOCNTIONNEEMTN
                $bien = 'Immeuble';
            }elseif($propertyDefinition == 'A définir'){
                $bien = 'Inconnu';
            }elseif($propertyDefinition == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }
            elseif($propertyDefinition == 'Parking / Garage'){
                $bien = 'Parking/box';
            }else{
                $bien = $propertyDefinition;
            }

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

            // Calcul des honoraires en %
            $honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
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
                        ${'url'.$key+1} = 'http://'.$app.'/images/galery/'.$photos[$key]['galeryFrontName']."?".$photos[$key]['createdAt']->format('Ymd');
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

            // BILAN DPE
            if($property['diagDpe'] > 0 and $property['diagDpe'] <= 50 ){
                $bilanDpe = 'A';
            }elseif($property['diagDpe'] > 50 and $property['diagDpe'] <= 90 ){
                $bilanDpe = 'B';
            }elseif($property['diagDpe'] > 90 and $property['diagDpe'] <= 150 ){
                $bilanDpe = 'C';
            }elseif($property['diagDpe'] > 150 and $property['diagDpe'] <= 230 ){
                $bilanDpe = 'D';
            }elseif($property['diagDpe'] > 230 and $property['diagDpe'] <= 330 ){
                $bilanDpe = 'E';
            }elseif($property['diagDpe'] > 330 and $property['diagDpe'] <= 450 ){
                $bilanDpe = 'F';
            }else{
                $bilanDpe = 'G';
            }

            // Bilan GES
            if($property['diagGes'] > 0 and $property['diagGes'] <= 50 ){
                $bilanGes = 'A';
            }elseif($property['diagGes'] > 50 and $property['diagGes'] <= 90 ){
                $bilanGes = 'B';
            }elseif($property['diagGes'] > 90 and $property['diagGes'] <= 150 ){
                $bilanGes = 'C';
            }elseif($property['diagGes'] > 150 and $property['diagGes'] <= 230 ){
                $bilanGes = 'D';
            }elseif($property['diagGes'] > 230 and $property['diagGes'] <= 330 ){
                $bilanGes = 'E';
            }elseif($property['diagGes'] > 330 and $property['diagGes'] <= 450 ){
                $bilanGes = 'F';
            }else{
                $bilanGes = 'G';
            }

            if($property['diagChoice'] == "obligatoire"){
                $diagDPEChoice = "D";
                $diagGESChoice = "E";
            }elseif($property['diagChoice'] == "vierge"){
                $diagDPEChoice = "VI";
                $diagGESChoice = "VI";
            }else{
                $diagDPEChoice = "NS";
                $diagGESChoice = "NS";
            }


            // Création d'une ligne du tableau
            $data = array(
                '"RC-1860977"',                                               // 1 - Identifiant Agence
                '"'.$property['ref'].'"',                                   // 2 - Référence agence du bien
                '"Vente"',                                                  // 3 - Type d’annonce
                '"'.$bien.'"',                                              // 4 - Type de bien
                '"'.$property['zipcode'].'"',                               // 5 - CP
                '"'.$property['city'].'"',                                  // 6 - Ville
                '"France"',                                                 // 7 - Pays
                '"'.$property['adress'].'"',                                // 8 - Adresse
                '""',                                                       // 9 - Quartier / Proximité
                '""',                                                       // 10 - Activités commerciales
                '"'.$property['priceFai'].'"',                              // 11 - Prix / Loyer / Prix de cession
                '""',                                                       // 12 - Loyer / mois murs
                '"0"',                                                      // 13 - Loyer CC
                '"0"',                                                      // 14 - Loyer HT
                '""',                                                       // 15 - Honoraires
                '"'.$property['surfaceHome'].'"',                           // 16 - Surface (m²)
                '"'.$property['surfaceLand'].'"',                           // 17 - Surface terrain (m²)
                '"'.$property['piece'].'"',                                 // 18 - NB de pièces
                '"'.$property['room'].'"',                                  // 19 - NB de chambres
                '"'.$property['name'].'"',                                  // 20 - Libellé
                '"'.$annonce.'"',                                           // 21 - Descriptif
                '"'.$property['disponibilityAt'].'"',                       // 22 - Date de disponibilité
                '""',                                                       // 23 - Charges
                '"'.$property['level'].'"',                                 // 24 - Etage
                '""',                                                       // 25 - NB d’étages
                '"'.$property['isFurnished'].'"',                           // 26 - Meublé
                '"'.$property['constructionAt'].'"',                        // 27 - Année de construction
                '""',                                                       // 28 - Refait à neuf
                '"'.$property['bathroom'].'"',                              // 29 - NB de salles de bain
                '"'.$property['sanitation'].'"',                            // 30 - NB de salles d’eau
                '"'.$property['wc'].'"',                                    // 31 - NB de WC
                '"0"',                                                      // 32 - WC séparés
                '"'.$property['slCode'].'"',                                // 33 - Type de chauffage
                '""',                                                       // 34 - Type de cuisine
                '"'.$sud.'"',                                               // 35 - Orientation sud
                '"'.$est.'"',                                               // 36 - Orientation est
                '"'.$ouest.'"',                                             // 37 - Orientation ouest
                '"'.$nord.'"',                                              // 38 - Orientation nord
                '"'.$property['balcony'].'"',                               // 39 - NB balcons
                '""',                                                       // 40 - SF Balcon
                '"0"',// 41 - Ascenseur
                '"0"',// 42 - Cave
                '""',                                                       // 43 - NB de parkings
                '"0"',                                                      // 44 - NB de boxes
                '"0"',// 45 - Digicode
                '"0"',// 46 - Interphone
                '"0"',// 47 - Gardien
                '"'.$terrace.'"',                                           // 48 - Terrasse
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
                '"'.$publications.'"',                                  // 82 - Publications
                '"0"',                                      // 83 - Mandat en exclusivité
                '"0"',                                      // 84 - Coup de cœur
                '"'.$url1.'"',                                              // 85 - Photo 1
                '"'.$url2.'"',                                              // 86 - Photo 2
                '"'.$url3.'"',                                              // 87 - Photo 3
                '"'.$url4.'"',                                              // 88 - Photo 4
                '"'.$url5.'"',                                              // 89 - Photo 5
                '"'.$url6.'"',                                              // 90 - Photo 6
                '"'.$url7.'"',                                              // 91 - Photo 7
                '"'.$url8.'"',                                              // 92 - Photo 8
                '"'.$url9.'"',                                              // 93 - Photo 9
                '"'.$titrephoto1.'"',                                       // 94 - Titre photo 1
                '"'.$titrephoto2.'"',                                       // 95 - Titre photo 2
                '"'.$titrephoto3.'"',                                       // 96 - Titre photo 3
                '"'.$titrephoto4.'"',                                       // 97 - Titre photo 4
                '"'.$titrephoto5.'"',                                       // 98 - Titre photo 5
                '"'.$titrephoto6.'"',                                       // 99 - Titre photo 6
                '"'.$titrephoto7.'"',                                       // 100 - Titre photo 7
                '"'.$titrephoto8.'"',                                       // 101 - Titre photo 8
                '"'.$titrephoto9.'"',                                       // 102 - Titre photo 9
                '""',                                                       // 103 - Photo panoramique
                '""',                                                       // 104 - URL visite virtuelle
                '"'.$property['gsm'].'"',                                   // 105 - Téléphone à afficher
                '"'.$property['firstName'].' '.$property['lastName'].'"',   // 106 - Contact à afficher
                '"'.$property['email'].'"',                                 // 107 - Email de contact
                '"'.$property['zipcode'].'"',                               // 108 - CP Réel du bien
                '"'.$property['city'].'"',                                  // 109 - Ville réelle du bien
                '""',                                                       // 110 - Inter-cabinet
                '""',                                                       // 111 - Inter-cabinet prive
                '"'.$refMandat.'"',                                         // 112 - N° de mandat
                '"'.$mandatAt.'"',                                          // 113 - Date mandat
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
                '"'.$url10.'"',                                             // 164 - Photo 10
                '"'.$url11.'"',                                             // 165 - Photo 11
                '"'.$url12.'"',                                             // 166 - Photo 12
                '"'.$url13.'"',                                             // 167 - Photo 13
                '"'.$url14.'"',                                             // 168 - Photo 14
                '"'.$url15.'"',                                             // 169 - Photo 15
                '"'.$url16.'"',                                             // 170 - Photo 16
                '"'.$url17.'"',                                             // 171 - Photo 17
                '"'.$url18.'"',                                             // 172 - Photo 18
                '"'.$url19.'"',                                             // 173 - Photo 19
                '"'.$url20.'"',                                             // 174 - Photo 20
                '""',                                                       // 175 - Identifiant technique
                '"'.$property['diagDpe'].'"',                               // 176 - Consommation énergie
                '"'.$diagDPEChoice.'"',                                     // 177 - Bilan consommation énergie
                '"'.$property['diagGes'].'"',                               // 178 - Emissions GES
                '"'.$diagGESChoice.'"',                                     // 179 - Bilan émission GES
                '""',                                                       // 180 - Identifiant quartier (obsolète)
                '"'.$property['ssCategory'].'"',                            // 181 - Sous type de bien
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
                '""',                                   // 193 - Transport : Ligne
                '""',                                   // 194 - Transport : Station
                '""',                                   // 195 - Durée bail
                '""',                                   // 196 - Places en salle
                '""',                                   // 197 - Monte-charge
                '""',                                   // 198 - Quai
                '""',                                   // 199 - Nombre de bureaux
                '""',                                   // 200 - Prix du droit d’entrée
                '""',                                   // 201 - Prix masqué
                '""',                                   // 202 - Loyer annuel global
                '""',                                   // 203 - Charges annuelles globales
                '""',                                   // 204 - Loyer annuel au m2
                '""',                                   // 205 - Charges annuelles au m2
                '"0"',                                  // 206 - Charges mensuelles  Loyer annuel CC HT
                '"0"',                                  // 207 - Loyer annuel CC
                '"0"',                                  // 208 - Loyer annuel HT
                '"0"',                                  // 209 - Charges annuelles HT
                '"0"',                                  // 210 - Loyer annuel au m2 CC
                '"0"',                                  // 211 - Loyer annuel au m2 HT
                '"0"',                                  // 212 - Charges annuelles au m2 HT
                '"0"',                                  // 213 - Divisible
                '""',                                   // 214 - Surface divisible minimale
                '""',                                   // 215 - Surface divisible maximale
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
                '"'.$property['copro'].'"',                                 // 258 - En copropriété
                '""',                                   // 259 - Nombre de lots
                '"'.$property['chargeCopro'].'"',                           // 260 - Charges annuelles
                '""',                                   // 261 - Syndicat des copropriétaires en procédure
                '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
                '""',                                   // 263 - Champ personnalisé 26
                '"'.$url21.'"',                                             // 264 - Photo 21
                '"'.$url22.'"',                                             // 265 - Photo 22
                '"'.$url23.'"',                                             // 266 - Photo 23
                '"'.$url24.'"',                                             // 267 - Photo 24
                '"'.$url25.'"',                                             // 268 - Photo 25
                '"'.$url26.'"',                                             // 269 - Photo 26
                '"'.$url27.'"',                                             // 270 - Photo 27
                '"'.$url28.'"',                                             // 271 - Photo 28
                '"'.$url29.'"',                                             // 272 - Photo 29
                '"'.$url30.'"',                                             // 273 - Photo 30
                '"'.$titrephoto10.'"',                                      // 274 - Titre photo 10
                '"'.$titrephoto11.'"',                                      // 275 - Titre photo 11
                '"'.$titrephoto12.'"',                                      // 276 - Titre photo 12
                '"'.$titrephoto13.'"',                                      // 277 - Titre photo 13
                '"'.$titrephoto14.'"',                                      // 278 - Titre photo 14
                '"'.$titrephoto15.'"',                                      // 279 - Titre photo 15
                '"'.$titrephoto16.'"',                                      // 280 - Titre photo 16
                '"'.$titrephoto17.'"',                                      // 281 - Titre photo 17
                '"'.$titrephoto18.'"',                                      // 282 - Titre photo 18
                '"'.$titrephoto19.'"',                                      // 283 - Titre photo 19
                '"'.$titrephoto20.'"',                                      // 284 - Titre photo 20
                '"'.$titrephoto21.'"',                                      // 285 - Titre photo 21
                '"'.$titrephoto22.'"',                                      // 286 - Titre photo 22
                '"'.$titrephoto23.'"',                                      // 287 - Titre photo 23
                '"'.$titrephoto24.'"',                                      // 288 - Titre photo 24
                '"'.$titrephoto25.'"',                                      // 289 - Titre photo 25
                '"'.$titrephoto26.'"',                                      // 290 - Titre photo 26
                '"'.$titrephoto27.'"',                                      // 291 - Titre photo 27
                '"'.$titrephoto28.'"',                                      // 292 - Titre photo 28
                '"'.$titrephoto29.'"',                                      // 293 - Titre photo 29
                '"'.$titrephoto30.'"',                                      // 294 - Titre photo 30
                '""',// 295 - Prix du terrain
                '""',// 296 - Prix du modèle de maison
                '""',// 297 - Nom de l'agence gérant le terrain
                '""',// 298 - Latitude
                '""',// 299 - Longitude
                '""',// 300 - Précision GPS
                '""',// 301 - Version Format
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
                '"'.$dpeAt.'"',                                             // 324 - Date réalisation DPE
                '""',                                                       // 325 - Version DPE
                '"'.$property['dpeEstimateEnergyDown'].'"',                 // 326 - DPE coût min conso
                '"'.$property['dpeEstimateEnergyUp'].'"',                   // 327 - DPE coût max conso
                '"'.$RefDPE.'"',                                            // 328 - DPE date référence conso
                '""',                                                       // 329 - Surface terrasse
                '""',                                                       // 330 - DPE coût conso annuelle
                '""',                                                       // 331 - Loyer de base
                '""',                                                       // 332 - Loyer de référence majoré
                '""',                                                       // 333 - Encadrement des loyers
            );
            $rows[] = implode('!#', $data);
        }
        $content = implode("\n", $rows);
        //dd($content);

        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/Annonces/Annonces.csv';                                  // Chemin du fichier
        if(file_exists($file))
        {
            unlink($file);                                                  // Suppression du précédent s'il existe
            file_put_contents('doc/report/Annonces/Annonces.csv', $content); // Génération du fichier dans l'arborescence du fichiers du site
        }
        file_put_contents('doc/report/Annonces/Annonces.csv', $content);     // Génération du fichier dans l'arborescence du fichiers du site

        // PARTIE III : Constitution du dossier zip
        $Rep = 'doc/report/Annonces/';
        $zip = new \ZipArchive();                                          // instanciation de la classe Zip
        if(is_dir($Rep))
        {
            if($zip->open('RC-1860977.zip', ZipArchive::CREATE) == TRUE)
            {
                $fichiers = scandir($Rep);
                unset($fichiers[0], $fichiers[1]);
                foreach($fichiers as $f)
                {
                    // On ajoute chaque fichier à l’archive en spécifiant l’argument optionnel.
                    // Pour ne pas créer de dossier dans l’archive.
                    if(!$zip->addFile($Rep.$f, $f))
                    {
                        dd('erreur');
                    }
                }
                $zip->close();
                rename('RC-1860977.zip', 'doc/report/RC-1860977.zip');
            }else{
                dd('Erreur');
            }
        }

        return $this->json([
            'code' => 200,
            'message' => 'Le fichier Zip a été correctement généré.' . $app
        ]);
    }

    /**
     * Génération du Fichiers CSV pour Figaro
     **/
    #[Route('/report/annoncesfigaro', name: 'app_gestapp_report_annonces')]
    public function PropertyCSV4(PropertyRepository $propertyRepository, PhotoRepository $photoRepository, ComplementRepository $complementRepository): Response
    {
        // PARTIE I : Génération du fichier CSV
        $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger
        $app = $this->container->get('router')->getContext()->getHost();    // On récupère l'url de l'appl pour les url des photos

        $rows = array();                                                    // Construction du tableau
        foreach ($properties as $property){
            // Description de l'annonce
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Contruction de la référence de l'anonnce
            $dup = $property['dup'];
            if($dup){
                $refProperty = $property['ref'].$dup;
                $refMandat = $property['refMandat'].$dup;
            }else{
                $refProperty = $property['ref'];
                $refMandat = $property['refMandat'];
            }

            // Sélection du type de bien
            $propertyDefinition = $property['propertyDefinition'];
            if($propertyDefinition == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($propertyDefinition == 'Vente'){                                    // A CORRIGER D'URGENCE POUR LE BON FOCNTIONNEEMTN
                $bien = 'Immeuble';
            }elseif($propertyDefinition == 'A définir'){
                $bien = 'Inconnu';
            }elseif($propertyDefinition == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }
            elseif($propertyDefinition == 'Parking / Garage'){
                $bien = 'Parking/box';
            }else{
                $bien = $propertyDefinition;
            }

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

            // Calcul des honoraires en %
            $honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
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
                        ${'url'.$key+1} = 'http://'.$app.'/images/galery/'.$photos[$key]['galeryFrontName']."?".$photos[$key]['createdAt']->format('Ymd');
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

            // BILAN DPE
            if($property['diagDpe'] > 0 and $property['diagDpe'] <= 50 ){
                $bilanDpe = 'A';
            }elseif($property['diagDpe'] > 50 and $property['diagDpe'] <= 90 ){
                $bilanDpe = 'B';
            }elseif($property['diagDpe'] > 90 and $property['diagDpe'] <= 150 ){
                $bilanDpe = 'C';
            }elseif($property['diagDpe'] > 150 and $property['diagDpe'] <= 230 ){
                $bilanDpe = 'D';
            }elseif($property['diagDpe'] > 230 and $property['diagDpe'] <= 330 ){
                $bilanDpe = 'E';
            }elseif($property['diagDpe'] > 330 and $property['diagDpe'] <= 450 ){
                $bilanDpe = 'F';
            }else{
                $bilanDpe = 'G';
            }

            // Bilan GES
            if($property['diagGes'] > 0 and $property['diagGes'] <= 50 ){
                $bilanGes = 'A';
            }elseif($property['diagGes'] > 50 and $property['diagGes'] <= 90 ){
                $bilanGes = 'B';
            }elseif($property['diagGes'] > 90 and $property['diagGes'] <= 150 ){
                $bilanGes = 'C';
            }elseif($property['diagGes'] > 150 and $property['diagGes'] <= 230 ){
                $bilanGes = 'D';
            }elseif($property['diagGes'] > 230 and $property['diagGes'] <= 330 ){
                $bilanGes = 'E';
            }elseif($property['diagGes'] > 330 and $property['diagGes'] <= 450 ){
                $bilanGes = 'F';
            }else{
                $bilanGes = 'G';
            }

            if($property['diagChoice'] == "obligatoire"){
                $diagDPEChoice = "D";
                $diagGESChoice = "E";
            }elseif($property['diagChoice'] == "vierge"){
                $diagDPEChoice = "VI";
                $diagGESChoice = "VI";
            }else{
                $diagDPEChoice = "NS";
                $diagGESChoice = "NS";
            }


            // Création d'une ligne du tableau
            $data = array(
                '"RC-1860977"',                                               // 1 - Identifiant Agence
                '"'.$property['ref'].'"',                                   // 2 - Référence agence du bien
                '"Vente"',                                                  // 3 - Type d’annonce
                '"'.$bien.'"',                                              // 4 - Type de bien
                '"'.$property['zipcode'].'"',                               // 5 - CP
                '"'.$property['city'].'"',                                  // 6 - Ville
                '"France"',                                                 // 7 - Pays
                '"'.$property['adress'].'"',                                // 8 - Adresse
                '""',                                                       // 9 - Quartier / Proximité
                '""',                                                       // 10 - Activités commerciales
                '"'.$property['priceFai'].'"',                              // 11 - Prix / Loyer / Prix de cession
                '""',                                                       // 12 - Loyer / mois murs
                '"0"',                                                      // 13 - Loyer CC
                '"0"',                                                      // 14 - Loyer HT
                '""',                                                       // 15 - Honoraires
                '"'.$property['surfaceHome'].'"',                           // 16 - Surface (m²)
                '"'.$property['surfaceLand'].'"',                           // 17 - Surface terrain (m²)
                '"'.$property['piece'].'"',                                 // 18 - NB de pièces
                '"'.$property['room'].'"',                                  // 19 - NB de chambres
                '"'.$property['name'].'"',                                  // 20 - Libellé
                '"'.$annonce.'"',                                           // 21 - Descriptif
                '"'.$property['disponibilityAt'].'"',                       // 22 - Date de disponibilité
                '""',                                                       // 23 - Charges
                '"'.$property['level'].'"',                                 // 24 - Etage
                '""',                                                       // 25 - NB d’étages
                '"'.$property['isFurnished'].'"',                           // 26 - Meublé
                '"'.$property['constructionAt'].'"',                        // 27 - Année de construction
                '""',                                                       // 28 - Refait à neuf
                '"'.$property['bathroom'].'"',                              // 29 - NB de salles de bain
                '"'.$property['sanitation'].'"',                            // 30 - NB de salles d’eau
                '"'.$property['wc'].'"',                                    // 31 - NB de WC
                '"0"',                                                      // 32 - WC séparés
                '"'.$property['slCode'].'"',                                // 33 - Type de chauffage
                '""',                                                       // 34 - Type de cuisine
                '"'.$sud.'"',                                               // 35 - Orientation sud
                '"'.$est.'"',                                               // 36 - Orientation est
                '"'.$ouest.'"',                                             // 37 - Orientation ouest
                '"'.$nord.'"',                                              // 38 - Orientation nord
                '"'.$property['balcony'].'"',                               // 39 - NB balcons
                '""',                                                       // 40 - SF Balcon
                '"0"',// 41 - Ascenseur
                '"0"',// 42 - Cave
                '""',                                                       // 43 - NB de parkings
                '"0"',                                                      // 44 - NB de boxes
                '"0"',// 45 - Digicode
                '"0"',// 46 - Interphone
                '"0"',// 47 - Gardien
                '"'.$terrace.'"',                                           // 48 - Terrasse
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
                '"'.$publications.'"',                                  // 82 - Publications
                '"0"',                                      // 83 - Mandat en exclusivité
                '"0"',                                      // 84 - Coup de cœur
                '"'.$url1.'"',                                              // 85 - Photo 1
                '"'.$url2.'"',                                              // 86 - Photo 2
                '"'.$url3.'"',                                              // 87 - Photo 3
                '"'.$url4.'"',                                              // 88 - Photo 4
                '"'.$url5.'"',                                              // 89 - Photo 5
                '"'.$url6.'"',                                              // 90 - Photo 6
                '"'.$url7.'"',                                              // 91 - Photo 7
                '"'.$url8.'"',                                              // 92 - Photo 8
                '"'.$url9.'"',                                              // 93 - Photo 9
                '"'.$titrephoto1.'"',                                       // 94 - Titre photo 1
                '"'.$titrephoto2.'"',                                       // 95 - Titre photo 2
                '"'.$titrephoto3.'"',                                       // 96 - Titre photo 3
                '"'.$titrephoto4.'"',                                       // 97 - Titre photo 4
                '"'.$titrephoto5.'"',                                       // 98 - Titre photo 5
                '"'.$titrephoto6.'"',                                       // 99 - Titre photo 6
                '"'.$titrephoto7.'"',                                       // 100 - Titre photo 7
                '"'.$titrephoto8.'"',                                       // 101 - Titre photo 8
                '"'.$titrephoto9.'"',                                       // 102 - Titre photo 9
                '""',                                                       // 103 - Photo panoramique
                '""',                                                       // 104 - URL visite virtuelle
                '"'.$property['gsm'].'"',                                   // 105 - Téléphone à afficher
                '"'.$property['firstName'].' '.$property['lastName'].'"',   // 106 - Contact à afficher
                '"'.$property['email'].'"',                                 // 107 - Email de contact
                '"'.$property['zipcode'].'"',                               // 108 - CP Réel du bien
                '"'.$property['city'].'"',                                  // 109 - Ville réelle du bien
                '""',                                                       // 110 - Inter-cabinet
                '""',                                                       // 111 - Inter-cabinet prive
                '"'.$refMandat.'"',                                         // 112 - N° de mandat
                '"'.$mandatAt.'"',                                          // 113 - Date mandat
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
                '"'.$url10.'"',                                             // 164 - Photo 10
                '"'.$url11.'"',                                             // 165 - Photo 11
                '"'.$url12.'"',                                             // 166 - Photo 12
                '"'.$url13.'"',                                             // 167 - Photo 13
                '"'.$url14.'"',                                             // 168 - Photo 14
                '"'.$url15.'"',                                             // 169 - Photo 15
                '"'.$url16.'"',                                             // 170 - Photo 16
                '"'.$url17.'"',                                             // 171 - Photo 17
                '"'.$url18.'"',                                             // 172 - Photo 18
                '"'.$url19.'"',                                             // 173 - Photo 19
                '"'.$url20.'"',                                             // 174 - Photo 20
                '""',                                                       // 175 - Identifiant technique
                '"'.$property['diagDpe'].'"',                               // 176 - Consommation énergie
                '"'.$diagDPEChoice.'"',                                     // 177 - Bilan consommation énergie
                '"'.$property['diagGes'].'"',                               // 178 - Emissions GES
                '"'.$diagGESChoice.'"',                                     // 179 - Bilan émission GES
                '""',                                                       // 180 - Identifiant quartier (obsolète)
                '"'.$property['ssCategory'].'"',                            // 181 - Sous type de bien
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
                '""',                                   // 193 - Transport : Ligne
                '""',                                   // 194 - Transport : Station
                '""',                                   // 195 - Durée bail
                '""',                                   // 196 - Places en salle
                '""',                                   // 197 - Monte-charge
                '""',                                   // 198 - Quai
                '""',                                   // 199 - Nombre de bureaux
                '""',                                   // 200 - Prix du droit d’entrée
                '""',                                   // 201 - Prix masqué
                '""',                                   // 202 - Loyer annuel global
                '""',                                   // 203 - Charges annuelles globales
                '""',                                   // 204 - Loyer annuel au m2
                '""',                                   // 205 - Charges annuelles au m2
                '"0"',                                  // 206 - Charges mensuelles  Loyer annuel CC HT
                '"0"',                                  // 207 - Loyer annuel CC
                '"0"',                                  // 208 - Loyer annuel HT
                '"0"',                                  // 209 - Charges annuelles HT
                '"0"',                                  // 210 - Loyer annuel au m2 CC
                '"0"',                                  // 211 - Loyer annuel au m2 HT
                '"0"',                                  // 212 - Charges annuelles au m2 HT
                '"0"',                                  // 213 - Divisible
                '""',                                   // 214 - Surface divisible minimale
                '""',                                   // 215 - Surface divisible maximale
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
                '"'.$property['copro'].'"',                                 // 258 - En copropriété
                '""',                                   // 259 - Nombre de lots
                '"'.$property['chargeCopro'].'"',                           // 260 - Charges annuelles
                '""',                                   // 261 - Syndicat des copropriétaires en procédure
                '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
                '""',                                   // 263 - Champ personnalisé 26
                '"'.$url21.'"',                                             // 264 - Photo 21
                '"'.$url22.'"',                                             // 265 - Photo 22
                '"'.$url23.'"',                                             // 266 - Photo 23
                '"'.$url24.'"',                                             // 267 - Photo 24
                '"'.$url25.'"',                                             // 268 - Photo 25
                '"'.$url26.'"',                                             // 269 - Photo 26
                '"'.$url27.'"',                                             // 270 - Photo 27
                '"'.$url28.'"',                                             // 271 - Photo 28
                '"'.$url29.'"',                                             // 272 - Photo 29
                '"'.$url30.'"',                                             // 273 - Photo 30
                '"'.$titrephoto10.'"',                                      // 274 - Titre photo 10
                '"'.$titrephoto11.'"',                                      // 275 - Titre photo 11
                '"'.$titrephoto12.'"',                                      // 276 - Titre photo 12
                '"'.$titrephoto13.'"',                                      // 277 - Titre photo 13
                '"'.$titrephoto14.'"',                                      // 278 - Titre photo 14
                '"'.$titrephoto15.'"',                                      // 279 - Titre photo 15
                '"'.$titrephoto16.'"',                                      // 280 - Titre photo 16
                '"'.$titrephoto17.'"',                                      // 281 - Titre photo 17
                '"'.$titrephoto18.'"',                                      // 282 - Titre photo 18
                '"'.$titrephoto19.'"',                                      // 283 - Titre photo 19
                '"'.$titrephoto20.'"',                                      // 284 - Titre photo 20
                '"'.$titrephoto21.'"',                                      // 285 - Titre photo 21
                '"'.$titrephoto22.'"',                                      // 286 - Titre photo 22
                '"'.$titrephoto23.'"',                                      // 287 - Titre photo 23
                '"'.$titrephoto24.'"',                                      // 288 - Titre photo 24
                '"'.$titrephoto25.'"',                                      // 289 - Titre photo 25
                '"'.$titrephoto26.'"',                                      // 290 - Titre photo 26
                '"'.$titrephoto27.'"',                                      // 291 - Titre photo 27
                '"'.$titrephoto28.'"',                                      // 292 - Titre photo 28
                '"'.$titrephoto29.'"',                                      // 293 - Titre photo 29
                '"'.$titrephoto30.'"',                                      // 294 - Titre photo 30
                '""',// 295 - Prix du terrain
                '""',// 296 - Prix du modèle de maison
                '""',// 297 - Nom de l'agence gérant le terrain
                '""',// 298 - Latitude
                '""',// 299 - Longitude
                '""',// 300 - Précision GPS
                '""',// 301 - Version Format
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
                '"'.$dpeAt.'"',                                             // 324 - Date réalisation DPE
                '""',                                                       // 325 - Version DPE
                '"'.$property['dpeEstimateEnergyDown'].'"',                 // 326 - DPE coût min conso
                '"'.$property['dpeEstimateEnergyUp'].'"',                   // 327 - DPE coût max conso
                '"'.$RefDPE.'"',                                            // 328 - DPE date référence conso
                '""',                                                       // 329 - Surface terrasse
                '""',                                                       // 330 - DPE coût conso annuelle
                '""',                                                       // 331 - Loyer de base
                '""',                                                       // 332 - Loyer de référence majoré
                '""',                                                       // 333 - Encadrement des loyers
            );
            $rows[] = implode('!#', $data);
        }
        $content = implode("\n", $rows);
        //dd($content);

        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/Annonces/Annonces.csv';                                  // Chemin du fichier
        if(file_exists($file))
        {
            unlink($file);                                                  // Suppression du précédent s'il existe
            file_put_contents('doc/report/Annonces/Annonces.csv', $content); // Génération du fichier dans l'arborescence du fichiers du site
        }
        file_put_contents('doc/report/Annonces/Annonces.csv', $content);     // Génération du fichier dans l'arborescence du fichiers du site

        // PARTIE III : Constitution du dossier zip
        $Rep = 'doc/report/Annonces/';
        $zip = new \ZipArchive();                                          // instanciation de la classe Zip
        if(is_dir($Rep))
        {
            if($zip->open('RC-1860977.zip', ZipArchive::CREATE) == TRUE)
            {
                $fichiers = scandir($Rep);
                unset($fichiers[0], $fichiers[1]);
                foreach($fichiers as $f)
                {
                    // On ajoute chaque fichier à l’archive en spécifiant l’argument optionnel.
                    // Pour ne pas créer de dossier dans l’archive.
                    if(!$zip->addFile($Rep.$f, $f))
                    {
                        dd('erreur');
                    }
                }
                $zip->close();
                rename('RC-1860977.zip', 'doc/report/RC-1860977.zip');
            }else{
                dd('Erreur');
            }
        }

        return $this->json([
            'code' => 200,
            'message' => 'Le fichier Zip a été correctement généré.' . $app
        ]);
    }

}
