<?php

namespace App\Service;


use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use phpseclib3\Net\SSH2;
use Symfony\Component\HttpFoundation\RequestStack;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use Twig\Environment;
use ZipArchive;
use App\Service\PropertyService;

class ftptransfertService
{
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        private Environment $twig,
        public PropertyService $propertyService,
        public string $urlftpseloger, public string $portftpseloger, public string $loginftpseloger, public string $passwordftpseloger,
        public string $urlftpfigaro, public string $portftpfigaro, public string $loginftpfigaro, public string $passwordftpfigaro,
        public string $urlftpga, public string $portftpga, public string $loginftpga, public string $passwordftpga,
        public string $urlftpvi, public string $portftpvi, public string $loginftpvi, public string $passwordftpvi,
    )
    {
        $this->requestStack = $requestStack;
    }

    public function selogerFTP(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger

        //dd($properties);

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
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Récupération de la reference
            $ref = $property['ref'];
            $refMandat = $property['refMandat'];

            // Sélection du type de bien
            $propertyDefinition = $property['rubric'];
            if($propertyDefinition == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($propertyDefinition == 'Vente'){                                    // A CORRIGER D'URGENCE POUR LE BON FOCNTIONNEEMTN
                $bien = 'Immeuble';
            }elseif($propertyDefinition == 'A définir'){
                $bien = 'Inconnu';
            }elseif($propertyDefinition == 'Loft'){
                $bien = 'loft/atelier/surface';
            }elseif($propertyDefinition == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }elseif($propertyDefinition == 'Parking'){
                $bien = 'Parking/box';
            }elseif($propertyDefinition == 'Garage'){
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
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);

            // Création d'une ligne du tableau
            $data = array(
                '"RC1860977"',                                                  // 1 - Identifiant Agence
                '"' . $ref . '"',                                       // 2 - Référence agence du bien
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
                '"' . $destination['rentChargeHonoraire'] . '"',                // 15 - Honoraires
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


        // IV. Dépôt sur le serveur de FTP
        $ftpserver = $this->urlftpseloger;
        $ftpport = $this->portftpseloger;
        $ftpusername = $this->loginftpseloger;
        $ftppassword = $this->passwordftpseloger;

        //dd('server : '.$ftpserver, 'password :'.$ftppassword, 'port : '.$ftpport, 'username : '.$ftpusername);

        // Connexion au serveur FTP
        $connId = ftp_ssl_connect($ftpserver, $ftpport);
        if (!$connId) {
            // Gestion des erreurs de connexion
            exit('Impossible de se connecter au serveur FTP.');
        }
        // Authentification FTP
        $login = ftp_login($connId, $ftpusername, $ftppassword);
        if (!$login) {
            // Gestion des erreurs d'authentification
            exit('Erreur lors de l\'authentification FTP.');
        }

        // Activer le mode passif
        ftp_pasv($connId, true);

        // Chemin du fichier local à transférer
        $fullHttp = $request->getUri();
        $parsedUrl = parse_url($fullHttp);
        if (!$port){
            $fichierLocal = $parsedUrl['scheme'].'://'.$parsedUrl['host'].'/doc/report/RC-1860977.zip';
        }else{
            $fichierLocal = $parsedUrl['scheme'].'://'.$parsedUrl['host'].':'.$port.'/doc/report/RC-1860977.zip';
        }
        // Chemin de destination sur le serveur FTP
        $cheminDestination = 'RC-1860977.zip';

        // Ouvrir le fichier local en lecture
        $fp = fopen($fichierLocal, 'r');
        // Transfert du fichier
        if (ftp_put($connId, $cheminDestination, $fichierLocal, FTP_BINARY)) {
            echo 'Le fichier a été transféré avec succès sur "SE LOGER".';
        } else {
            // Gestion des erreurs de transfert
            echo 'Téléversement sur "SE loger" - Erreur lors du transfert du fichier sur le serveur FTP.';
        }

        // Fermeture du flux et de la connexion FTP
        fclose($fp);
        ftp_close($connId);
    }

    public function figaroFTP(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        // PARTIE I : Génération du fichier CSV
        $properties = $propertyRepository->reportpropertyfigaroFTP();           // On récupère les biens à publier sur SeLoger

        // Création de l'url pour les photos
        $fullHttp = $request->getUri();
        $scheme = parse_url($fullHttp, PHP_URL_SCHEME);
        $port = parse_url($fullHttp, PHP_URL_PORT);
        $host = parse_url($fullHttp, PHP_URL_HOST);
        if (!$port){
            $app = $scheme.'://'.$host;
        }else{
            $app = $scheme.'://'.$host.':'.$port;
        }

        $rows = array();                                                        // Construction du tableau
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            // Description de l'annonce
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonce = strip_tags($data, '<br>');
            //dd($annonce);

            // Récupération de la reference
            $ref = $property['ref'];
            $refMandat = $property['refMandat'];

            // Sélection du type de bien
            $propertyDefinition = $property['rubric'];
            if($propertyDefinition == 'Propriété / Château') {
                $bien = 'Château';
            }elseif($propertyDefinition == 'Vente'){
                $bien = 'Immeuble';
            }elseif($propertyDefinition == 'A définir'){
                $bien = 'Inconnu';
            }elseif($propertyDefinition == 'Loft'){
                $bien = 'loft/atelier/surface';
            }elseif($propertyDefinition == 'Atelier'){
                $bien = 'loft/atelier/surface';
            }elseif($propertyDefinition == 'Parking'){
                $bien = 'Parking/box';
            }elseif($propertyDefinition == 'Garage'){
                $bien = 'Parking/box';
            }else{
                $bien = $propertyDefinition;
            }

            // Préparation de la date dpeAt
            if ($property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
            }else{
                $dpeAt ="";
            }

            // Préparation de la date de création mandat
            if ($property['mandatAt'] instanceof \DateTime) {
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
            // $honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
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
            $publications = 'Figaro';

            // Transformation terrace en booléen
            if($property['terrace']){
                $terrace = 1;
            }else{
                $terrace = 0;
            }

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id'=> $idcomplement]);

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);

            // création SSfamille
            $ssfamile = $property['rubricss'];


            // Création d'une ligne du tableau
            $data = array(
                '"107428"',                                                 // 1 - Identifiant Agence
                '"' . $ref . '"',                                           // 2 - Référence agence du bien
                '"' . $destination['destination'] . '"',                    // 3 - Type d’annonce
                '"' . $destination['typeBien'] . '"',                       // 4 - Type de bien
                '"' . $property['zipcode'] . '"',                           // 5 - CP
                '"' . $property['city'] . '"',                              // 6 - Ville
                '"France"',                                                 // 7 - Pays
                '"' . $property['adress'] . '"',                            // 8 - Adresse
                '""',                                                       // 9 - Quartier / Proximité
                '""',                                                       // 10 - Activités commerciales
                '"' . $property['priceFai'] . '"',                              // 11 - Prix / Loyer / Prix de cession
                '"' . $destination['rent'] . '"',                              // 12 - Loyer / mois murs
                '"' . $destination['rentCC'] . '"',                             // 13 - Loyer CC
                '"' . $destination['rentHT'] . '"',                             // 14 - Loyer HT
                '"' . $destination['rentChargeHonoraire'] . '"',                // 15 - Honoraires
                '"' . $property['surfaceHome'] . '"',                           // 16 - Surface (m²)
                '"' . $property['surfaceLand'] . '"',                           // 17 - Surface terrain (m²)
                '"' . $property['piece'] . '"',                                 // 18 - NB de pièces
                '"' . $property['room'] . '"',                                  // 19 - NB de chambres
                '"' . $property['name'] . '"',                                  // 20 - Libellé
                '"' . $annonce . '"',                                           // 21 - Descriptif
                '"' . $property['disponibilityAt'] . '"',                       // 22 - Date de disponibilité
                '""',                                                       // 23 - Charges
                '"' . $property['level'] . '"',                                 // 24 - Etage
                '""',                                                       // 25 - NB d’étages
                '"' . $property['isFurnished'] . '"',                           // 26 - Meublé
                '"' . $property['constructionAt'] . '"',                        // 27 - Année de construction
                '""',                                                       // 28 - Refait à neuf
                '"' . $property['bathroom'] . '"',                              // 29 - NB de salles de bain
                '"' . $property['sanitation'] . '"',                            // 30 - NB de salles d’eau
                '"' . $property['wc'] . '"',                                    // 31 - NB de WC
                '"0"',                                                      // 32 - WC séparés
                '"' . $property['slCode'] . '"',                                // 33 - Type de chauffage
                '""',                                                       // 34 - Type de cuisine
                '"' . $sud . '"',                                               // 35 - Orientation sud
                '"' . $est . '"',                                               // 36 - Orientation est
                '"' . $ouest . '"',                                             // 37 - Orientation ouest
                '"' . $nord . '"',                                              // 38 - Orientation nord
                '"' . $property['balcony'] . '"',                               // 39 - NB balcons
                '""',                                                       // 40 - SF Balcon
                '"0"',// 41 - Ascenseur
                '"0"',// 42 - Cave
                '""',                                                       // 43 - NB de parkings
                '"0"',                                                      // 44 - NB de boxes
                '"0"',// 45 - Digicode
                '"0"',// 46 - Interphone
                '"0"',// 47 - Gardien
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
                '"' . $property['firstName'].' '.$property['lastName'].'"',     // 106 - Contact à afficher
                '"' . $property['email'] . '"',                                 // 107 - Email de contact
                '"' . $property['zipcode'] . '"',                               // 108 - CP Réel du bien
                '"' . $property['city'] . '"',                                  // 109 - Ville réelle du bien
                '""',                                                           // 110 - Inter-cabinet
                '""',                                                           // 111 - Inter-cabinet prive
                '"' . $refMandat . '"',                                         // 112 - N° de mandat
            '"' . $mandatAt . '"',                                              // 113 - Date mandat
                '""',                                                           // 114 - Nom mandataire
                '""',                                                           // 115 - Prénom mandataire
                '""',                                                           // 116 - Raison sociale mandataire
                '""',                                                           // 117 - Adresse mandataire
                '""',                                                           // 118 - CP mandataire
                '""',                                                           // 119 - Ville mandataire
                '""',                                                           // 120 - Téléphone mandataire
                '""',                                                           // 121 - Commentaires mandataire
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

        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/Annoncesfigaro/Annonces.csv';                                  // Chemin du fichier
        if(file_exists($file))
        {
            unlink($file);                                                  // Suppression du précédent s'il existe
            file_put_contents('doc/report/Annoncesfigaro/Annonces.csv', $content); // Génération du fichier dans l'arborescence du fichiers du site
        }
        file_put_contents('doc/report/Annoncesfigaro/Annonces.csv', $content);     // Génération du fichier dans l'arborescence du fichiers du site

        // PARTIE III : Constitution du dossier zip
        $Rep = 'doc/report/Annoncesfigaro/';
        $zip = new \ZipArchive();                                          // instanciation de la classe Zip
        if(is_dir($Rep))
        {
            if($zip->open('107428.zip', ZipArchive::CREATE) == TRUE)
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
                rename('107428.zip', 'doc/report/107428.zip');
            }else{
                dd('Erreur');
            }
        }

        // IV. Dépôt sur le serveur de FTP
        $ftpserver = $this->urlftpfigaro;
        $ftpport = $this->portftpfigaro;
        $ftpusername = $this->loginftpfigaro;
        $ftppassword = $this->passwordftpfigaro;
        // Connexion au serveur FTP
        $connId = ftp_connect($ftpserver, $ftpport);
        if (!$connId) {
            // Gestion des erreurs de connexion
            exit('Impossible de se connecter au serveur FTP.');
        }
        // Authentification FTP
        $login = ftp_login($connId, $ftpusername, $ftppassword);
        if (!$login) {
            // Gestion des erreurs d'authentification
            exit('Erreur lors de l\'authentification FTP.');
        }

        $fullHttp = $request->getUri();
        $parsedUrl = parse_url($fullHttp);
        if (!$port){
            $fichierLocal = $parsedUrl['scheme'].'://'.$parsedUrl['host'].'/doc/report/107428.zip';
        }else{
            $fichierLocal = $parsedUrl['scheme'].'://'.$parsedUrl['host'].':'.$port.'/doc/report/107428.zip';
        }
        // Chemin de destination sur le serveur FTP
        $cheminDestination = '107428.zip';

        // Transfert du fichier
        if (ftp_put($connId, $cheminDestination, $fichierLocal, FTP_BINARY)) {
            echo 'Le fichier a été transféré avec succès sur "Figaro".';
        } else {
            // Gestion des erreurs de transfert
            echo 'Téléversement sur "Figaro Immo" - Erreur lors du transfert du fichier sur le serveur FTP.';
        }

        // Fermeture de la connexion FTP
        ftp_close($connId);
    }

    public function greenacresFTP(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        // PARTIE I : Génération du fichier XML
        $properties = $propertyRepository->reportpropertyGreenacresFTP();            // On récupère les biens à publier sur SeLoger

        // Création de l'url pour les photos
        $fullHttp = $request->getUri();
        $scheme = parse_url($fullHttp, PHP_URL_SCHEME);
        $port = parse_url($fullHttp, PHP_URL_PORT);
        $host = parse_url($fullHttp, PHP_URL_HOST);
        if (!$port){
            $app = $scheme.'://'.$host;
        }else{
            $app = $scheme.'://'.$host.':'.$port;
        }

        $adverts = [];                                                    // Construction du tableau
        foreach ($properties as $property) {
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $property = $propertyRepository->find($property['id']);
            //dd($property);

            $charge = $destination['rentCharge'];

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
            //$honoraires = round(100 - (($property->getPrice() * 100) / $property->getPriceFai()), 2);

            // Récupération des images liées au bien
            $photos = $photoRepository->findNameBy(['property' => $property->getId()]);

            $pics = [];
            if (!$photos) {                                                                       // Si aucune photo présente
                $pics = [];
            }else {
                foreach($photos as $photo)
                {
                    $urlphoto = $app . '/properties/'.$photo['path'].'/'. $photo['galeryFrontName'];
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
            // $publications = 'SL';

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
                'pics' => $pics,
                'charge' => $charge
            ];
            array_push($adverts, $xml);

        }

        $xmlContent = $this->twig->render('gestapp/report/greenacrees.html.twig', [
            'adverts' => $adverts
        ]);


        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/AnnoncesGreen/892318a.xml';                                  // Chemin du fichier
        if (file_exists($file)) {
            unlink($file);                                                  // Suppression du précédent s'il exist
            file_put_contents('doc/report/AnnoncesGreen/892318a.xml', $xmlContent); // Génération du fichier dans l'arborescence du fichiers du site
        }
        file_put_contents('doc/report/AnnoncesGreen/892318a.xml', $xmlContent);     // Génération du fichier dans l'arborescence du fichiers du site

        // IV. Dépôt sur le serveur de FTP GREEN ACRES
        // -------------------------------------------

        // Chemin du fichier local à transférer
        if (!$port){
            $fichierLocal = $scheme.'://'.$host.'/doc/report/AnnoncesGreen/892318a.xml';
        }else{
            $fichierLocal = $scheme.'://'.$host.':'.$port.'/doc/report/AnnoncesGreen/892318a.xml';
        }
        // Chemin de destination sur le serveur FTP
        $cheminDestination = '892318a.xml';

        $ftpserver = $this->urlftpga;
        $ftpport = $this->portftpga;
        $ftpusername = $this->loginftpga;
        $ftppassword = $this->passwordftpga;

        //dd('server : '.$ftpserver, 'password :'.$ftppassword, 'port : '.$ftpport, 'username : '.$ftpusername);
        // Connexion au serveur FTP
        $connId = ftp_connect($ftpserver, $ftpport);
        if (!$connId) {
            // Gestion des erreurs de connexion
            exit('Impossible de se connecter au serveur FTP.');
        }
        // Authentification FTP
        $login = ftp_login($connId, $ftpusername, $ftppassword);
        if (!$login) {
            // Gestion des erreurs d'authentification
            exit('Erreur lors de l\'authentification FTP.');
        }
        // Transfert du fichier
        if (ftp_put($connId, $cheminDestination, $fichierLocal, FTP_BINARY)) {
            echo 'Le fichier a été transféré avec succès sur "GreenAcres".';
        } else {
            // Gestion des erreurs de transfert
            echo 'Erreur lors du transfert du fichier sur le serveur FTP.';
        }
        // Fermeture de la connexion FTP
        ftp_close($connId);

        // IV. Dépôt sur le serveur de FTP VIZZIT
        // -------------------------------------------
        $ftpserver2 = $this->urlftpvi;
        $ftpport2 = $this->portftpvi;
        $ftpusername2 = $this->loginftpvi;
        $ftppassword2 = $this->passwordftpvi;
        // Connexion au serveur FTP
        $connId2 = ftp_connect($ftpserver2, $ftpport2);
        if (!$connId2) {
            // Gestion des erreurs de connexion
            exit('Impossible de se connecter au serveur FTP.');
        }
        // Authentification FTP
        $login2 = ftp_login($connId2, $ftpusername2, $ftppassword2);
        if (!$login2) {
            // Gestion des erreurs d'authentification
            exit('Erreur lors de l\'authentification FTP.');
        }
        // Transfert du fichier
        if (ftp_put($connId2, $cheminDestination, $fichierLocal, FTP_BINARY)) {
            echo 'Le fichier a été transféré avec succès sur VIZZIT.';
        } else {
            // Gestion des erreurs de transfert
            echo 'Erreur lors du transfert du fichier sur le serveur FTP.';
        }
        // Fermeture de la connexion FTP
        ftp_close($connId2);
    }
}