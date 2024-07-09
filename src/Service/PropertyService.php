<?php

namespace App\Service;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PropertyService
{
    public function __construct(
        public  EntityManagerInterface $em,
        public PropertyRepository $propertyRepository
    )
    {}

    public function getAnnonce($property){
        $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
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

    public function getDir(Property $property,){

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

    public function arraySLFIG(
        $property,
        $ref,
        $destination,
        $bien,
        $annonce,
        $disponibilityAt,
        $sud,
        $nord,
        $ouest,
        $est,
        $terrace,
        $publications
    ){
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
    }

}