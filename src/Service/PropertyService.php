<?php

namespace App\Service;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PropertyService
{
    public function __construct(
        public  EntityManagerInterface $em,
        public PropertyRepository $propertyRepository,
        public PhotoRepository $photoRepository,
        protected RequestStack $request,
        protected UrlGeneratorInterface $urlGenerator
    )
    {}

    public function getAnnonce(Property $property){
        $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property->getAnnonce()) );
        $annonce = strip_tags($data, '<br>');

        return  $annonce;
    }

    // Destination commerciale du bien (Vente particulier, vente commerce, location particulier, vente commerce)
    public function getDestination(Property $property)
    {
        $famille = $property->getFamily()->getId();
        $rubric = $property->getRubric()->getId();
        //$rubricss = $property->getRubricss()->getId();
        //dd($famille);
        if($famille == 8 || $famille == 6){
            //dd($famille);
            $destination = 'vente';
            $typeBien = $property->getRubric()->getName();
            $price = $property->getPrice();
            $priceFai = $property->getPriceFai();
            $rent = "";
            $rentCharge = "";
            $rentWithCharge = "";
            $rentChargeModsPayment = "";
            $warrantyDeposit = "";
            $rentChargeHonoraire = "";
            $commerceAnnualRentGlobal = "";
            $rentCC = "";
            $rentHT = "";
            $rentWallMonth = "";
            $commerceAnnualChargeRentGlobal = "";
            $commerceAnnualRentMeter = "";
            $commerceAnnualChargeRentMeter = "";
            $commerceChargeRentMonthHt = "";
            $commerceRentAnnualCc = "";
            $commerceRentAnnualHt = "";
            $commerceChargeRentAnnualHt = "";
            $commerceRentAnnualMeterCc = "";
            $commerceRentAnnualMeterHt = "";
            $commerceChargeRentAnnualMeterHt = "";
            $commerceSurfaceDivisible = "";
            $commerceSurfaceDivisibleMin = "";
            $commerceSurfaceDivisibleMax = "";
        }elseif($famille == 5){
            //dd('location immobilier');
            $destination = 'location';
            $typeBien = $property->getRubric()->getName();
            $price = "";
            $priceFai = "";
            $rent = $property->getRent();
            $rentCharge = $property->getRentCharge();
            $rentWithCharge = $rent + $rentCharge;
            $warrantyDeposit = $property->getWarrantyDeposit();
            $rentChargeModsPayment = $property->getRentChargeModsPayment();
            $rentChargeHonoraire = $property->getRentChargeHonoraire();
            $rentCC = $property->isRentCC();
            $rentHT = $property->isRentHT();
            $rentWallMonth = $property->getRentWallMonth();
            $commerceAnnualRentGlobal = "";
            $commerceAnnualChargeRentGlobal = "";
            $commerceAnnualRentMeter = "";
            $commerceAnnualChargeRentMeter = "";
            $commerceChargeRentMonthHt = "";
            $commerceRentAnnualCc = "";
            $commerceRentAnnualHt = "";
            $commerceChargeRentAnnualHt = "";
            $commerceRentAnnualMeterCc = "";
            $commerceRentAnnualMeterHt = "";
            $commerceChargeRentAnnualMeterHt = "";
            $commerceSurfaceDivisible = "";
            $commerceSurfaceDivisibleMin = "";
            $commerceSurfaceDivisibleMax = "";
        }elseif($famille == 4 && $rubric == 8){
            //dd('location pro');
            $destination = 'location';
            $typeBien = $property->getRubricss()->getName();
            $price = "";
            $priceFai = "";
            $rent = $property->getRent();
            $rentCharge = $property->getRentCharge();
            $rentWithCharge = $rent + $rentCharge;
            $warrantyDeposit = $property->getWarrantyDeposit();
            $rentChargeModsPayment = $property->getRentChargeModsPayment();
            $rentChargeHonoraire = $property->getRentChargeHonoraire();
            $rentCC = $property->isRentCC();
            $rentHT = $property->isRentHT();
            $rentWallMonth = $property->getRentWallMonth();
            $commerceAnnualRentGlobal = $property->getCommerceAnnualRentGlobal();
            $commerceAnnualChargeRentGlobal = $property->getCommerceAnnualChargeRentGlobal();
            $commerceAnnualRentMeter = $property->getCommerceAnnualRentMeter();
            $commerceAnnualChargeRentMeter = $property->getCommerceAnnualChargeRentMeter();
            if($property->IsCommerceChargeRentMonthHt() == 0){
                $commerceChargeRentMonthHt = 'non';
            }else{
                $commerceChargeRentMonthHt = 'oui';
            }
            if($property->IsCommerceRentAnnualCc() == 0){
                $commerceRentAnnualCc = 'non';
            }else{
                $commerceRentAnnualCc = 'oui';
            }
            if($property->IsCommerceRentAnnualHt() == 0){
                $commerceRentAnnualHt = 'non';
            }else{
                $commerceRentAnnualHt = 'oui';
            }
            if($property->IsCommerceChargeRentAnnualHt() == 0){
                $commerceChargeRentAnnualHt = 'non';
            }else{
                $commerceChargeRentAnnualHt = 'oui';
            }
            if($property->IsCommerceRentAnnualMeterCc() == 0){
                $commerceRentAnnualMeterCc = 'non';
            }else{
                $commerceRentAnnualMeterCc = 'oui';
            }
            if($property->IsCommerceRentAnnualMeterHt() == 0){
                $commerceRentAnnualMeterHt = 'non';
            }else{
                $commerceRentAnnualMeterHt = 'oui';
            }
            if($property->IsCommerceChargeRentAnnualMeterHt() == 0){
                $commerceChargeRentAnnualMeterHt = 'non';
            }else{
                $commerceChargeRentAnnualMeterHt = 'oui';
            }
            if($property->IsCommerceSurfaceDivisible() == 0){
                $commerceSurfaceDivisible = 'non';
            }else{
                $commerceSurfaceDivisible = 'oui';
            }
            $commerceSurfaceDivisibleMin = $property->getCommerceSurfaceDivisibleMin();
            $commerceSurfaceDivisibleMax = $property->getCommerceSurfaceDivisibleMax();
        }
        return array(
            'destination' => $destination, 'typeBien' => $typeBien,
            'price' => $price, 'priceFai' => $priceFai,
            'rent' => $rent, 'rentCharge' => $rentCharge, 'rentWithCharge' => $rentWithCharge, 'rentChargeModsPayment' => $rentChargeModsPayment, 'rentChargeHonoraire' => $rentChargeHonoraire,
            'rentCC' => $rentCC, 'rentHT' => $rentHT, 'rentWallMonth' => $rentWallMonth,
            'warrantyDeposit' => $warrantyDeposit,
            'commerceAnnualRentGlobal' => $commerceAnnualRentGlobal, 'commerceAnnualChargeRentGlobal' => $commerceAnnualChargeRentGlobal, 'commerceAnnualRentMeter' => $commerceAnnualRentMeter,
            'commerceAnnualChargeRentMeter' => $commerceAnnualChargeRentMeter, 'commerceChargeRentMonthHt' => $commerceChargeRentMonthHt, 'commerceRentAnnualCc' => $commerceRentAnnualCc,
            'commerceRentAnnualHt'=> $commerceRentAnnualHt, 'commerceChargeRentAnnualHt' => $commerceChargeRentAnnualHt, 'commerceRentAnnualMeterCc' => $commerceRentAnnualMeterCc,
            'commerceRentAnnualMeterHt' => $commerceRentAnnualMeterHt, 'commerceChargeRentAnnualMeterHt'=>$commerceChargeRentAnnualMeterHt, 'commerceSurfaceDivisible'=>$commerceSurfaceDivisible,
            'commerceSurfaceDivisibleMin'=>$commerceSurfaceDivisibleMin, 'commerceSurfaceDivisibleMax'=>$commerceSurfaceDivisibleMax
        );
    }

    // Génération des références pour les diffuseurs
    public function getRefs(Property $property, PropertyRepository $propertyRepository)
    {
        // Vérification si property été dupliqué
        $properties = $propertyRepository->findBy(['RefMandat' => $property->getRefMandat()]);
        //dd(count($properties));
        if(count($properties) > 1)
        {
            $lastProperty = end($properties);
            $dup = $lastProperty->getDupMandat();
            $dup++;
            $ref = $lastProperty->getRef();;
            $initRef = substr($ref, 0,-1 );
            $newRef = $initRef.$dup;
        }else{
            $dup = 'A';
            $ref = $property->getRef();
            $newRef = $ref.$dup;
        }
        return array('ref'=>$newRef, 'dup'=> $dup);
    }

    public function getDates($property)
    {
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
            $refDPE = $property['RefDPE']->format('d/m/Y');
        }else{
            $refDPE ="";
        }

        // Préparation de la date de disponibilité
        if ($property['disponibilityAt'] instanceof \DateTime) {
            $disponibilityAt = $property['disponibilityAt']->format('d/m/Y');
        }else{
            $disponibilityAt ="";
        }

        $dates = ['dpeAt' => $dpeAt, 'mandatAt' => $mandatAt, 'disponibilityAt' => $disponibilityAt, 'refDPE' => $refDPE ];

        return $dates;
    }

    // Détermination des classes des diagnostique dpe et ges
    public function getClasseDpe(Property $property){
        if($property->getDiagChoice() == "obligatoire"){
            // Bilan GES
            if($property->getDiagDpe() > 0 and $property->getDiagDpe() <= 50 ){
                $bilanDpe = 'A';
            }elseif($property->getDiagDpe() > 50 and $property->getDiagDpe() <= 90 ){
                $bilanDpe = 'B';
            }elseif($property->getDiagDpe() > 90 and $property->getDiagDpe()<= 150 ){
                $bilanDpe = 'C';
            }elseif($property->getDiagDpe() > 150 and $property->getDiagDpe() <= 230 ){
                $bilanDpe = 'D';
            }elseif($property->getDiagDpe() > 230 and $property->getDiagDpe() <= 330 ){
                $bilanDpe = 'E';
            }elseif($property->getDiagDpe() > 330 and $property->getDiagDpe() <= 450 ){
                $bilanDpe = 'F';
            }else{
                $bilanDpe = 'G';
            }
        }elseif($property->getDiagChoice() == "vierge"){
            $bilanDpe = "VI";
        }else{
            $bilanDpe = "NS";
        }
        return $bilanDpe;
    }

    public function getClasseGes(Property $property){
        if($property->getDiagChoice() == "obligatoire"){
            // Bilan GES
            if($property->getDiagGes() > 0 and $property->getDiagGes() <= 50 ){
                $bilanGes = 'A';
            }elseif($property->getDiagGes() > 50 and $property->getDiagGes() <= 90 ){
                $bilanGes = 'B';
            }elseif($property->getDiagGes() > 90 and $property->getDiagGes() <= 150 ){
                $bilanGes = 'C';
            }elseif($property->getDiagGes() > 150 and $property->getDiagGes() <= 230 ){
                $bilanGes = 'D';
            }elseif($property->getDiagGes() > 230 and $property->getDiagGes() <= 330 ){
                $bilanGes = 'E';
            }elseif($property->getDiagGes() > 330 and $property->getDiagGes() <= 450 ){
                $bilanGes = 'F';
            }else{
                $bilanGes = 'G';
            }
        }elseif($property->getDiagChoice() == "vierge"){
            $bilanGes = "VI";
        }else{
            $bilanGes = "NS";
        }
        return $bilanGes;
    }

    // Archivage des biens en expiration de mandat
    public function expireAtOut(Property $property, PublicationRepository $publicationRepository, EntityManagerInterface $em)
    {

        $publish = $publicationRepository->findOneBy(['id'=> $property->getPublication()]);
        $publish->setIsWebpublish(0);
        $publish->setIsPublishMeilleur(0);
        $publish->setIsPublishParven(0);
        $publish->setIsPublishleboncoin(0);
        $publish->setIsPublishgreenacres(0);
        $publish->setIsPublishfigaro(0);
        $publish->setIsPublishseloger(0);
        $publish->setIsSocialNetwork(0);
        $em->persist($publish);

        $property->setIsArchived(1);
        $property->setArchivedAt(new \DateTime('+90 days'));
        $em->persist($property);

        $em->flush();
    }

    public function getDir(Property $property){

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $ref = explode("/", $property->getRef());
        $refDir = $ref[0].'-'.$ref[1];

        return $refDir;
    }

    public function getBien($property)
    {
        if($property['family'] == 'Vente immobilier')
        {
            if($property['rubric'] == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($property['rubric'] == 'vente'){                                    // A CORRIGER D'URGENCE POUR LE BON FOCNTIONNEEMTN
                $bien = 'Immeuble';
            }elseif($property['rubric'] == 'A définir'){
                $bien = 'Inconnu';
            }elseif($property['rubric'] == 'Loft'){
                $bien = 'loft/atelier/surface';
            }elseif($property['rubric'] == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }elseif($property['rubric'] == 'Parking'){
                $bien = 'Parking/box';
            }elseif($property['rubric'] == 'Garage'){
                $bien = 'Parking/box';
            }elseif($property['rubric'] == 'Location'){
                $bien = $property['rubricss'];
            }else{
                $bien = $property['rubric'];
            }
        }else if($property['family'] == 'Immobilier professionnel')
        {
            if($property['rubric'] == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($property['rubric'] == 'vente'){                                    // A CORRIGER D'URGENCE POUR LE BON FOCNTIONNEEMTN
                $bien = 'Immeuble';
            }elseif($property['rubric'] == 'A définir'){
                $bien = 'Inconnu';
            }elseif($property['rubric'] == 'Loft'){
                $bien = 'loft/atelier/surface';
            }elseif($property['rubric'] == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }elseif($property['rubric'] == 'Parking'){
                $bien = 'Parking/box';
            }elseif($property['rubric'] == 'Garage'){
                $bien = 'Parking/box';
            }elseif($property['rubric'] == 'Location'){
                $bien = $property['rubricss'];
            }else{
                $bien = $property['rubric'];
            }
        }else if($property['family'] == 'Vente commerce, Reprise')
        {
            if($property['rubric'] == 'vente') {
                $bien = 'boutique';
            }else{
                $bien = 'bureaux';
            }
        }else
        {
            $bien = 'a détérminer';
        }

        return $bien;
    }

    public function getUrlPhotos($property)
    {
        $request = $this->request->getCurrentRequest();
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

        $photos = $this->photoRepository->findNameBy(['property' => $property['id']]);
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
            return $url;
        }else{
            $url = [];
            $titrephoto = [];
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
                    array_push($titrephoto, ${'titrephoto'.$key+1});
                }else{
                    ${'titrephoto'.$key+1} = '';
                    array_push($titrephoto, ${'titrephoto'.$key+1});
                }
            }
            return $url;
        }
    }

    public function getTitrePhotos($property)
    {
        $request = $this->request->getCurrentRequest();
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

        $photos = $this->photoRepository->findNameBy(['property' => $property['id']]);
        if(!$photos){                                                                       // Si aucune photo présente
            $titrephoto = [];
            // génération des titres de photos
            for ($i = 1; $i<31; $i++){
                ${'titrephoto'.$i} = '';
                array_push($titrephoto, ${'titrephoto'.$i});
            }
            return $titrephoto;
        }else{
            $titrephoto = [];
            $arraykey = array_keys($photos);
            // génération des titres de photos
            for ($key = 0; $key<30; $key++){
                if(array_key_exists($key,$arraykey)){
                    ${'titrephoto'.$key+1} = 'Photo-'.$property['ref'].'-'.$key+1;
                    array_push($titrephoto, ${'titrephoto'.$key+1});
                }else{
                    ${'titrephoto'.$key+1} = '';
                    array_push($titrephoto, ${'titrephoto'.$key+1});
                }
            }
            return $titrephoto;
        }
    }

    // Génération des lignes du tableau au format POLIRIS 4.11
    public function arrayRow(Property $propriete, $destination, $dates, $infos, $url, $titrephoto, $property, $version){
        $data = array(
            1 => '"' . $infos['refDossier'] . '"',                                  // 1 - Identifiant Agence
            2 => '"' . $property['ref'] . '"',                                      // 2 - Référence agence du bien
            3 => '"' . $destination['destination'] . '"',                           // 3 - Type d’annonce
            4 => '"' . $this->getBien($property) . '"',                             // 4 - Type de bien
            5 => '"' . $property['zipcode'] . '"',                                  // 5 - CP
            6 => '"' . $property['city'] . '"',                                     // 6 - Ville
            7 => '"France"',                                                        // 7 - Pays
            8 => '"' . $property['adress'] . '"',                                   // 8 - Adresse
            9 => '""',                                                              // 9 - Quartier / Proximité
            10 => '""',                                                             // 10 - Activités commerciales
            11 => '"' . $property['priceFai'] . '"',                                // 11 - Prix / Loyer / Prix de cession
            12 => '"' . $destination['rent'] . '"',                                 // 12 - Loyer / mois murs
            13 => '"' . $destination['rentCC'] . '"',                               // 13 - Loyer CC
            14 => '"' . $destination['rentHT'] . '"',                               // 14 - Loyer HT
            15 => '"' . $destination['rentChargeHonoraire'] . '"',                  // 15 - Honoraires
            16 => '"' . $property['surfaceHome'] . '"',                             // 16 - Surface (m²)
            17 => '"' . $property['surfaceLand'] . '"',                             // 17 - Surface terrain (m²)
            18 => '"' . $property['piece'] . '"',                                   // 18 - NB de pièces
            19 => '"' . $property['room'] . '"',                                    // 19 - NB de chambres
            20 => '"' . $property['name'] . '"',                                    // 20 - Libellé
            21 => '"' . $this->getAnnonce($propriete) . '"',                        // 21 - Descriptif
            22 => '"' . $dates['disponibilityAt'] . '"',                            // 22 - Date de disponibilité
            23 => '""',                                                             // 23 - Charges
            24 => '"' . $property['level'] . '"',                                   // 24 - Etage
            25 => '""',                                                             // 25 - NB d’étages
            26 => '"' . $property['isFurnished'] . '"',                             // 26 - Meublé
            27 => '"' . $property['constructionAt'] . '"',                          // 27 - Année de construction
            28 => '""',                                                             // 28 - Refait à neuf
            29 => '"' . $property['bathroom'] . '"',                                // 29 - NB de salles de bain
            30 => '"' . $property['sanitation'] . '"',                              // 30 - NB de salles d’eau
            31 => '"' . $property['wc'] . '"',                                      // 31 - NB de WC
            32 => '"0"',                                                            // 32 - WC séparés
            33 => '"' . $property['slCode'] . '"',                                  // 33 - Type de chauffage
            34 => '""',                                                             // 34 - Type de cuisine
            35 => '"' . $infos['sud'] . '"',                                        // 35 - Orientation sud
            36 => '"' . $infos['est'] . '"',                                        // 36 - Orientation est
            37 => '"' . $infos['ouest'] . '"',                                      // 37 - Orientation ouest
            38 => '"' . $infos['nord'] . '"',                                       // 38 - Orientation nord
            39 => '"' . $property['balcony'] . '"',                                 // 39 - NB balcons
            40 => '""',                                                             // 40 - SF Balcon
            41 => '"0"',                                                            // 41 - Ascenseur
            42 => '"0"',                                                            // 42 - Cave
            43 => '""',                                                             // 43 - NB de parkings
            44 => '"0"',                                                            // 44 - NB de boxes
            45 => '"0"',                                                            // 45 - Digicode
            46 => '"0"',                                                            // 46 - Interphone
            47 => '"0"',                                                            // 47 - Gardien
            48 => '"' . $infos['terrace'] . '"',                                    // 48 - Terrasse
            49 => '""',                                                             // 49 - Prix semaine Basse Saison
            50 => '""',                                                             // 50 - Prix quinzaine Basse Saison
            51 => '""',                                                             // 51 - Prix mois / Basse Saison
            52 => '""',                                                             // 52 - Prix semaine Haute Saison
            53 => '""',                                                             // 53 - Prix quinzaine Haute Saison
            54 => '""',                                                             // 54 - Prix mois Haute Saison
            55 => '""',                                                             // 55 - NB de personnes
            56 => '""',                                                             // 56 - Type de résidence
            57 => '""',                                                             // 57 - Situation
            58 => '""',                                                             // 58 - NB de couverts
            59 => '""',                                                             // 59 - NB de lits doubles
            60 => '""',                                                             // 60 - NB de lits simples
            61 => '"0"',                                                            // 61 - Alarme
            62 => '"0"',                                                            // 62 - Câble TV
            63 => '"0"',                                                            // 63 - Calme
            64 => '"0"',                                                            // 64 - Climatisation
            65 => '"0"',                                                            // 65 - Piscine
            66 => '"0"',                                                            // 66 - Aménagement pour handicapés
            67 => '"0"',                                                            // 67 - Animaux acceptés
            68 => '"0"',                                                            // 68 - Cheminée
            69 => '"0"',                                                            // 69 - Congélateur
            70 => '"0"',                                                            // 70 - Four
            71 => '"0"',                                                            // 71 - Lave-vaisselle
            72 => '"0"',                                                            // 72 - Micro-ondes
            73 => '"0"',                                                            // 73 - Placards
            74 => '"0"',                                                            // 74 - Téléphone
            75 => '"0"',                                                            // 75 - Proche lac
            76 => '"0"',                                                            // 76 - Proche tennis
            77 => '"0"',                                                            // 77 - Proche pistes de ski
            78 => '"0"',                                                            // 78 - Vue dégagée
            79 => '""',                                                             // 79 - Chiffre d’affaire
            80 => '""',                                                             // 80 - Longueur façade (m)
            81 => '"0"',                                                            // 81 - Duplex
            82 => '"' . $infos['publications'] . '"',                               // 82 - Publications
            83 => '"0"',                                                            // 83 - Mandat en exclusivité
            84 => '"0"',                                                            // 84 - Coup de cœur
            85 => '"' . $url[0] . '"',                                              // 85 - Photo 1
            86 => '"' . $url[1] . '"',                                              // 86 - Photo 2
            87 => '"' . $url[2] . '"',                                              // 87 - Photo 3
            88 => '"' . $url[3] . '"',                                              // 88 - Photo 4
            89 => '"' . $url[4] . '"',                                              // 89 - Photo 5
            90 => '"' . $url[5] . '"',                                              // 90 - Photo 6
            91 => '"' . $url[6] . '"',                                              // 91 - Photo 7
            92 => '"' . $url[7] . '"',                                              // 92 - Photo 8
            93 => '"' . $url[8] . '"',                                              // 93 - Photo 9
            94 => '"' . $titrephoto[0] . '"',                                       // 94 - Titre photo 1
            95 => '"' . $titrephoto[1] . '"',                                       // 95 - Titre photo 2
            96 => '"' . $titrephoto[2] . '"',                                       // 96 - Titre photo 3
            97 => '"' . $titrephoto[3] . '"',                                       // 97 - Titre photo 4
            98 => '"' . $titrephoto[4] . '"',                                       // 98 - Titre photo 5
            99 => '"' . $titrephoto[6] . '"',                                       // 99 - Titre photo 6
            100 => '"' . $titrephoto[7] . '"',                                      // 100 - Titre photo 7
            101 => '"' . $titrephoto[8] . '"',                                      // 101 - Titre photo 8
            102 => '"' . $titrephoto[9] . '"',                                      // 102 - Titre photo 9
            103 => '""',                                                            // 103 - Photo panoramique
            104 => '""',                                                            // 104 - URL visite virtuelle
            105 => '"' . $property['gsm'] . '"',                                    // 105 - Téléphone à afficher
            106 => '"' . $property['firstName'] . ' ' . $property['lastName'] . '"',// 106 - Contact à afficher
            107 => '"' . $property['email'] . '"',                                  // 107 - Email de contact
            108 => '"' . $property['zipcode'] . '"',                                // 108 - CP Réel du bien
            109 => '"' . $property['city'] . '"',                                   // 109 - Ville réelle du bien
            110 => '""',                                                            // 110 - Inter-cabinet
            111 => '""',                                                            // 111 - Inter-cabinet prive
            112 => '"' . $property['refMandat'] . '"',                              // 112 - N° de mandat
            113 => '"' . $dates['mandatAt'] . '"',                                  // 113 - Date mandat
            114 => '""',                                                            // 114 - Nom mandataire
            115 => '""',                                                            // 115 - Prénom mandataire
            116 => '""',                                                            // 116 - Raison sociale mandataire
            117 => '""',                                                            // 117 - Adresse mandataire
            118 => '""',                                                            // 118 - CP mandataire
            119 => '""',                                                            // 119 - Ville mandataire
            120 => '""',                                                            // 120 - Téléphone mandataire
            121 => '""',                                                            // 121 - Commentaires mandataire
            122 => '""',                                                            // 122 - Commentaires privés
            123 => '""',                                                            // 123 - Code négociateur
            124 => '""',                                                            // 124 - Code Langue 1
            125 => '""',                                                            // 125 - Proximité Langue 1
            126 => '""',                                                            // 126 - Libellé Langue 1
            127 => '""',                                                            // 127 - Descriptif Langue 1
            128 => '""',                                                            // 128 - Code Langue 2
            129 => '""',                                                            // 129 - Proximité Langue 2
            130 => '""',                                                       // 130 - Libellé Langue 2
            131 => '""',                                                       // 131 - Descriptif Langue 2
            132 => '""',                                                       // 132 - Code Langue 3
            133 => '""',                                                       // 133 - Proximité Langue 3
            134 => '""',                                                       // 134 - Libellé Langue 3
            135 => '""',                                                       // 135 - Descriptif Langue 3
            136 => '""',                                                       // 136 - Champ personnalisé 1
            137 => '""',                                                       // 137 - Champ personnalisé 2
            138 => '""',                                                       // 138 - Champ personnalisé 3
            139 => '""',                                                       // 139 - Champ personnalisé 4
            140 => '""',                                                       // 140 - Champ personnalisé 5
            141 => '""',                                                       // 141 - Champ personnalisé 6
            142 => '""',                                                       // 142 - Champ personnalisé 7
            143 => '""',                                                       // 143 - Champ personnalisé 8
            144 => '""',                                                       // 144 - Champ personnalisé 9
            145 => '""',                                                       // 145 - Champ personnalisé 10
            146 => '""',                                                       // 146 - Champ personnalisé 11
            147 => '""',                                                       // 147 - Champ personnalisé 12
            148 => '""',                                                       // 148 - Champ personnalisé 13
            149 => '""',                                                       // 149 - Champ personnalisé 14
            150 => '""',                                                       // 150 - Champ personnalisé 15
            151 => '""',                                                       // 151 - Champ personnalisé 16
            152 => '""',                                                       // 152 - Champ personnalisé 17
            153 => '""',                                                       // 153 - Champ personnalisé 18
            154 => '""',                                                       // 154 - Champ personnalisé 19
            155 => '""',                                                       // 155 - Champ personnalisé 20
            156 => '""',                                                       // 156 - Champ personnalisé 21
            157 => '""',                                                       // 157 - Champ personnalisé 22
            158 => '""',                                                       // 158 - Champ personnalisé 23
            159 => '""',                                                       // 159 - Champ personnalisé 24
            160 => '""',                                                       // 160 - Champ personnalisé 25
            161 => '""',                                                       // 161 - Dépôt de garantie
            162 => '"0"',                                                      // 162 - Récent
            163 => '"0"',                                                      // 163 - Travaux à prévoir
            164 => '"' . $url[9] . '"',                                                // 164 - Photo 10
            165 => '"' . $url[10] . '"',                                               // 165 - Photo 11
            166 => '"' . $url[11] . '"',                                               // 166 - Photo 12
            167 => '"' . $url[12] . '"',                                               // 167 - Photo 13
            168 => '"' . $url[13] . '"',                                               // 168 - Photo 14
            169 => '"' . $url[14] . '"',                                               // 169 - Photo 15
            170 => '"' . $url[15] . '"',                                               // 170 - Photo 16
            171 => '"' . $url[16] . '"',                                               // 171 - Photo 17
            172 => '"' . $url[17] . '"',                                               // 172 - Photo 18
            173 => '"' . $url[18] . '"',                                               // 173 - Photo 19
            174 => '"' . $url[19] . '"',                                               // 174 - Photo 20
            175 => '""',                                                               // 175 - Identifiant technique
            176 => '"' . $property['diagDpe'] . '"',                               // 176 - Consommation énergie
            177 => '"' . $this->getClasseDpe($propriete) . '"',                                          // 177 - Bilan consommation énergie
            178 => '"' . $property['diagGes'] . '"',                               // 178 - Emissions GES
            179 => '"' . $this->getClasseGes($propriete) . '"',                                          // 179 - Bilan émission GES
            180 => '""',                                                           // 180 - Identifiant quartier (obsolète)
            181 => '"' . $property['ssCategory'] . '"',                            // 181 - Sous type de bien
            182 => '""',                                                       // 182 - Périodes de disponibilité
            183 => '""',                                                       // 183 - Périodes basse saison
            184 => '""',                                                       // 184 - Périodes haute saison
            185 => '""',                                                       // 185 - Prix du bouquet
            186 => '""',                                                       // 186 - Rente mensuelle
            187 => '""',                                                       // 187 - Age de l’homme
            188 => '""',                                                       // 188 - Age de la femme
            189 => '"0"',                                                      // 189 - Entrée
            190 => '"0"',                                                      // 190 - Résidence
            191 => '"0"',                                                      // 191 - Parquet
            192 => '"0"',                                                      // 192 - Vis-à-vis
            193 => '""',                                                       // 193 - Transport : Ligne
            194 => '""',                                                       // 194 - Transport : Station
            195 => '""',                                                       // 195 - Durée bail
            196 => '""',                                                       // 196 - Places en salle
            197 => '""',                                                       // 197 - Monte-charge
            198 => '""',                                                       // 198 - Quai
            199 => '""',                                                       // 199 - Nombre de bureaux
            200 => '""',                                                       // 200 - Prix du droit d’entrée
            201 => '""',                                                       // 201 - Prix masqué
            202 => '"'.$destination['commerceAnnualRentGlobal'].'"',           // 202 - Loyer annuel global
            203 => '"'.$destination['commerceAnnualChargeRentGlobal'].'"',     // 203 - Charges annuelles globales
            204 => '"'.$destination['commerceAnnualRentMeter'].'"',            // 204 - Loyer annuel au m2
            205 => '"'.$destination['commerceAnnualChargeRentMeter'].'"',      // 205 - Charges annuelles au m2
            206 => '"'.$destination['commerceChargeRentMonthHt'].'"',          // 206 - Charges mensuelles  Loyer annuel CC HT
            207 => '"'.$destination['commerceRentAnnualCc'].'"',               // 207 - Loyer annuel CC
            208 => '"'.$destination['commerceRentAnnualHt'].'"',               // 208 - Loyer annuel HT
            209 => '"'.$destination['commerceChargeRentAnnualHt'].'"',         // 209 - Charges annuelles HT
            210 => '"'.$destination['commerceRentAnnualMeterCc'].'"',          // 210 - Loyer annuel au m2 CC
            211 => '"'.$destination['commerceRentAnnualMeterHt'].'"',          // 211 - Loyer annuel au m2 HT
            212 => '"'.$destination['commerceChargeRentAnnualMeterHt'].'"',    // 212 - Charges annuelles au m2 HT
            213 => '"'.$destination['commerceSurfaceDivisible'].'"',           // 213 - Divisible
            214 => '"'.$destination['commerceSurfaceDivisibleMin'].'"',        // 214 - Surface divisible minimale
            215 => '"'.$destination['commerceSurfaceDivisibleMax'].'"',        // 215 - Surface divisible maximale
            216 => '""',                                   // 216 - Surface séjour
            217 => '""',                                   // 217 - Nombre de véhicules
            218 => '""',                                   // 218 - Prix du droit au bail
            219 => '""',                                   // 219 - Valeur à l’achat
            220 => '""',                                   // 220 - Répartition du chiffre d’affaire
            221 => '""',                                   // 221 - Terrain agricole
            222 => '""',                                   // 222 - Equipement bébé
            223 => '""',                                   // 223 - Terrain constructible
            224 => '""',                                   // 224 - Résultat Année N-2
            225 => '""',                                   // 225 - Résultat Année N-1
            226 => '""',                                   // 226 - Résultat Actuel
            227 => '""',                                   // 227 - Immeuble de parkings
            228 => '""',                                   // 228 - Parking isolé
            229 => '""',                                   // 229 - Si Viager Vendu Libre Logement à
            230 => '""',                                   // 230 - Logement à disposition
            231 => '""',                                   // 231 - Terrain en pente
            232 => '""',                                   // 232 - Plan d’eau
            233 => '""',                                   // 233 - Lave-linge
            234 => '""',                                   // 234 - Sèche-linge
            235 => '""',                                   // 235 - Connexion internet
            236 => '""',                                   // 236 - Chiffre affaire Année N-2
            237 => '""',                                   // 237 - Chiffre affaire Année N-1
            238 => '""',                                   // 238 - Conditions financières
            239 => '""',                                   // 239 - Prestations diverses
            240 => '""',                                   // 240 - Longueur façade
            241 => '""',                                   // 241 - Montant du rapport
            242 => '""',                                   // 242 - Nature du bail
            243 => '""',                                   // 243 - Nature bail commercial
            244 => '""',                                   // 244 - Nombre terrasses
            245 => '""',                                   // 245 - Prix hors taxes
            246 => '""',                                   // 246 - Si Salle à manger
            247 => '""',                                   // 247 - Si Séjour
            248 => '""',                                   // 248 - Terrain donne sur la rue
            249 => '""',                                   // 249 - Immeuble de type bureaux
            250 => '""',                                   // 250 - Terrain viabilisé
            251 => '""',                                   // 251 - Equipement Vidéo
            252 => '""',                                   // 252 - Surface de la cave
            253 => '""',                                   // 253 - Surface de la salle à manger
            254 => '""',                                   // 254 - Situation commerciale
            255 => '""',                                   // 255 - Surface maximale d’un bureau
            256 => '""',                                   // 256 - Honoraires charge acquéreur (obsolète)
            257 => '""',                                   // 257 - Pourcentage honoraires TTC (obsolète)
            258 => '"' . $property['copro'] . '"',                                 // 258 - En copropriété
            259 => '""',                                   // 259 - Nombre de lots
            260 => '"' . $property['chargeCopro'] . '"',                           // 260 - Charges annuelles
            261 => '""',                                   // 261 - Syndicat des copropriétaires en procédure
            262 => '""',                                   // 262 - Détail procédure du syndicat des copropriétaires
            263 => '""',                                   // 263 - Champ personnalisé 26
            264 => '"' . $url[20] . '"',                                             // 264 - Photo 21
            265 => '"' . $url[21] . '"',                                             // 265 - Photo 22
            266 => '"' . $url[22] . '"',                                             // 266 - Photo 23
            267 => '"' . $url[23] . '"',                                             // 267 - Photo 24
            268 => '"' . $url[24] . '"',                                             // 268 - Photo 25
            269 => '"' . $url[25] . '"',                                             // 269 - Photo 26
            270 => '"' . $url[26] . '"',                                             // 270 - Photo 27
            271 => '"' . $url[27] . '"',                                             // 271 - Photo 28
            272 => '"' . $url[28] . '"',                                             // 272 - Photo 29
            273 => '"' . $url[29] . '"',                                             // 273 - Photo 30
            274 => '"' . $titrephoto[9] . '"',                                      // 274 - Titre photo 10
            275 => '"' . $titrephoto[10] . '"',                                      // 275 - Titre photo 11
            276 => '"' . $titrephoto[11] . '"',                                      // 276 - Titre photo 12
            277 => '"' . $titrephoto[12] . '"',                                      // 277 - Titre photo 13
            278 => '"' . $titrephoto[13] . '"',                                      // 278 - Titre photo 14
            279 => '"' . $titrephoto[14] . '"',                                      // 279 - Titre photo 15
            280 => '"' . $titrephoto[15] . '"',                                      // 280 - Titre photo 16
            281 => '"' . $titrephoto[16] . '"',                                      // 281 - Titre photo 17
            282 => '"' . $titrephoto[17] . '"',                                      // 282 - Titre photo 18
            283 => '"' . $titrephoto[18] . '"',                                      // 283 - Titre photo 19
            284 => '"' . $titrephoto[19] . '"',                                      // 284 - Titre photo 20
            285 => '"' . $titrephoto[20] . '"',                                      // 285 - Titre photo 21
            286 => '"' . $titrephoto[21] . '"',                                      // 286 - Titre photo 22
            287 => '"' . $titrephoto[22] . '"',                                      // 287 - Titre photo 23
            288 => '"' . $titrephoto[23] . '"',                                      // 288 - Titre photo 24
            289 => '"' . $titrephoto[24] . '"',                                      // 289 - Titre photo 25
            290 => '"' . $titrephoto[25] . '"',                                      // 290 - Titre photo 26
            291 => '"' . $titrephoto[26] . '"',                                      // 291 - Titre photo 27
            292 => '"' . $titrephoto[27] . '"',                                      // 292 - Titre photo 28
            293 => '"' . $titrephoto[28] . '"',                                      // 293 - Titre photo 29
            294 => '"' . $titrephoto[19] . '"',                                      // 294 - Titre photo 30
            295 => '""',// 295 - Prix du terrain
            296 =>  '""',// 296 - Prix du modèle de maison
            297 => '""',// 297 - Nom de l'agence gérant le terrain
            298 => '""',// 298 - Latitude
            299 => '""',// 299 - Longitude
            300 => '""',// 300 - Précision GPS
            301 => '"' . $infos['version'] . '"',                                                 // 301 - Version Format
            302 => '""',// 302 - Honoraires à la charge de l'acquéreur
            303 => '""',// 303 - Prix hors honoraires acquéreur
            304 => '""',// 304 - Modalités charges locataire
            305 => '""',// 305 - Complément loyer
            306 => '""',// 306 - Part honoraires état des lieux
            307 => '""',// 307 - URL du Barème des honoraires de l’Agence
            308 => '""',// 308 - Prix minimum
            309 => '""',// 309 - Prix maximum
            310 => '""',// 310 - Surface minimale
            311 => '""',// 311 - Surface maximale
            312 => '""',// 312 - Nombre de pièces minimum
            313 => '""',// 313 - Nombre de pièces maximum
            314 => '""',// 314 - Nombre de chambres minimum
            315 => '""',// 315 - Nombre de chambres maximum
            316 => '""',// 316 - ID type étage
            317 => '""',// 317 - Si combles aménageables
            318 => '""',// 318 - Si garage
            319 => '""',// 319 - ID type garage
            320 => '""',// 320 - Si possibilité mitoyenneté
            321 => '""',// 321 - Surface terrain nécessaire
            322 => '""',// 322 - Localisation
            323 => '""',// 323 - Nom du modèle
            324 => '"' . $dates['dpeAt'] . '"',                                     // 324 - Date réalisation DPE
            325 => '""',                                                            // 325 - Version DPE
            326 => '"' . $property['dpeEstimateEnergyDown'] . '"',                  // 326 - DPE coût min conso
            327 => '"' . $property['dpeEstimateEnergyUp'] . '"',                    // 327 - DPE coût max conso
            328 => '"' . $dates['refDPE'] . '"',                                    // 328 - DPE date référence conso
            329 => '""',                                                            // 329 - Surface terrasse
            330 => ((float)$version >= 4.10 ? '""': null),                               // 330 - DPE coût conso annuelle
            331 => ((float)$version >= 4.11 ? '""': null),                               // 331 - Loyer de base
            332 => ((float)$version >= 4.11 ? '""': null),                               // 332 - Loyer de référence majoré
            333 => ((float)$version >= 4.11 ? '""': null),                               // 333 - Encadrement des loyers
            334 => ((float)$version >= 4.12 ? '""': null),                               // 334 - Consommation annuelle au m2 en énergie finale
        );

        // Retire les champs si null
        if (is_null($data[330])) {
            unset($data[330]);
        }
        if (is_null($data[331])) {
            unset($data[331]);
        }
        if (is_null($data[332])) {
            unset($data[332]);
        }
        if (is_null($data[333])) {
            unset($data[333]);
        }
        if (is_null($data[334])) {
            unset($data[334]);
        }



        return $data;
    }
}