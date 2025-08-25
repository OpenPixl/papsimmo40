<?php

namespace App\Service;


use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use phpseclib3\Net\SSH2;
use Symfony\Component\HttpFoundation\RequestStack;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
        string $projectDir
    )
    {
        $this->requestStack = $requestStack;
        $this->projectDir = $projectDir;
    }

    public function generateExcel($data, $Rep, $nameFile)
    {
        // 2. Créer le fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. En-têtes
        $sheet->fromArray(['ID', 'Référence', 'Titre', 'Adresse', 'Prix', 'Mise à jour'], null, 'A1');

        // 4. Données
        $date = new \DateTime('now');
        $row = 2;
        $letter = 'A';
        foreach ($data as $d) {
            $sheet->setCellValue("A$row", $d['id']);
            $sheet->setCellValue("B$row", $d['ref']);
            $sheet->setCellValue("C$row", $d['name']);
            $sheet->setCellValue("D$row", $d['city']);
            $sheet->setCellValue("E$row", $d['priceFai']);
            $sheet->setCellValue("F$row", $date->format('d/m/Y'));
            $row++;
            $letter++;
        }

        $filePath = $this->projectDir . $Rep .$nameFile.'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        if(file_exists($filePath))
        {
            unlink($filePath);                                  // Suppression du précédent s'il existe
            $writer->save($filePath);                           // Génération du fichier dans l'arborescence du fichiers du site
        }
        $writer->save($filePath);

        return $filePath; // pour info, log, ou lien de téléchargement

    }

    public function directoryZip($Rep, $nameRep, $nameFile, $content, $nameFTP ){
        $zip = new \ZipArchive();                               // instanciation de la classe Zip
        $repFile = $Rep.$nameFile.'.csv';


        if(is_dir($Rep)) {
            if(file_exists($repFile))
            {
                unlink($repFile);                                   // Suppression du précédent s'il existe
                file_put_contents($repFile, $content);              // Génération du fichier dans l'arborescence du fichiers du site
            }
            file_put_contents($repFile, $content);                  // Génération du fichier dans l'arborescence du fichiers du site

            if($zip->open($nameFTP.'.zip', ZipArchive::CREATE) == TRUE)
            {
                $fichiers = scandir($Rep);
                unset($fichiers[0], $fichiers[1]);
                foreach($fichiers as $f)
                {
                    // Vérifie que le fichier a bien l'extension .xlsx (insensible à la casse)
                    if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'csv') {
                        if (!$zip->addFile($Rep . '/' . $f, $f)) {
                            dd('Erreur lors de l\'ajout du fichier : ' . $f);
                        }
                    }
                }
                $zip->close();
                rename($nameFTP.'.zip', 'doc/report/'.$nameFTP.'.zip');
            }else{
                dd('Erreur');
            }
        }else{
            mkdir($Rep."/", 0775, true);
            if(file_exists($repFile))
            {
                unlink($repFile);                                   // Suppression du précédent s'il existe
                file_put_contents($repFile, $content);              // Génération du fichier dans l'arborescence du fichiers du site
            }
            file_put_contents($repFile, $content);                  // Génération du fichier dans l'arborescence du fichiers du site

            if($zip->open($nameFTP.'.zip', ZipArchive::CREATE) == TRUE)
            {
                $fichiers = scandir($Rep);
                unset($fichiers[0], $fichiers[1]);
                foreach($fichiers as $f)
                {
                    // Vérifie que le fichier a bien l'extension .xlsx (insensible à la casse)
                    if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'csv') {
                        if (!$zip->addFile($Rep . '/' . $f, $f)) {
                            dd('Erreur lors de l\'ajout du fichier : ' . $f);
                        }
                    }
                }
                $zip->close();
                rename($nameFTP.'.zip', 'doc/report/'.$nameFTP.'.zip');
            }else{
                dd('Erreur');
            }
        }
    }

    public function selogerFTP(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $properties = $propertyRepository->reportpropertycsv3();            // On récupère les biens à publier sur SeLoger
        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);

            $dates = $this->propertyService->getDates($property);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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

            // Complements du bien
            $complement = $propriete->getOptions();
            $energies = $complement->getEnergies();

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'Annonces';             // Nom du dossier
        $nameFile = 'RC-1860977';               // Nom du Fichier sans extension
        $Rep = 'doc/report/Annonces/';     // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "RC-1860977");
        $this->generateExcel($properties, $Rep, $nameFile);
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

        $rows = array();                                                        // Construction du tableau
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);

            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }

        $content = implode("\n", $rows);

        // PARTIE II : Génération du fichier CSV
        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'figaro';                     // Nom du dossier
        $nameFile = 'Annonces';               // Nom du Fichier sans extension
        $Rep = 'doc/report/figaro/';             // nom du répertoire final
        $nameFTP = '107428';
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, $nameFTP);
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme GreenACRES et VIZZIT - XML
    public function greenacresFTP(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        // PARTIE I
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

        $adverts = []; // Construction du tableau
        $adverts2 = [];
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

            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            $energies = $this->propertyService->getEnergies($propriete);

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
                'type' => $rubric->getName(),
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
                'heating' => $energies,
                'pics' => $pics,
                'charge' => $charge
            ];
            array_push($adverts, $xml);


        }
        $xmlContent = $this->twig->render('gestapp/report/greenacrees.html.twig', [
            'adverts' => $adverts
        ]);


        // PARTIE II : Génération du fichier CSV
        $Rep = 'doc/report/Green/';
        $nameFile = '892318a';
        $repFile = $Rep.$nameFile.'.xml';
        if(is_dir($Rep)){
            if (file_exists($repFile)) {
                unlink($repFile);                                                  // Suppression du précédent s'il exist
                file_put_contents($repFile, $xmlContent); // Génération du fichier dans l'arborescence du fichiers du site
            }
            file_put_contents($repFile, $xmlContent);     // Génération du fichier dans l'arborescence du fichiers du site
        }else{
            mkdir($Rep."/", 0775, true);
            if (file_exists($repFile)) {
                unlink($repFile);                                                  // Suppression du précédent s'il exist
                file_put_contents($repFile, $xmlContent); // Génération du fichier dans l'arborescence du fichiers du site
            }
            file_put_contents($repFile, $xmlContent);     // Génération du fichier dans l'arborescence du fichiers du site
        }
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme Superimmo - poliris 4.11
    public function superimmo(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'SI';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);

            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'SI';
            // version du document
            $version = '4.11';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

            $infos = ['refDossier' => 'papsimmo', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
            //dd($equipments);

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);
        //dd($content);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'Superimmo';                     // Nom du dossier
        $nameFile = 'paps_superimmo';               // Nom du Fichier sans extension
        $Rep = 'doc/report/Superimmo/';             // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "Superimmo");
        $this->generateExcel($properties, $Rep, $nameFile);

    }

    // Protocole de transfert des annonces pour la plateforme Superimmo - poliris 4.09
    public function alentoor(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'AL';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);

            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'AL';
            // version du document
            $version = '4.09';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

            $infos = ['refDossier' => 'papsimmo', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
            //dd($equipments);

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);

            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'Alentour';             // Nom du dossier
        $nameFile = 'paps_alentour';               // Nom du Fichier sans extension
        $Rep = 'doc/report/Alentour/';     // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "Alentour");
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme Superimmo - poliris 4.12
    public function ht_louer(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'HT';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);


            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'HT';
            // version du document
            $version = '4.12';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}
            $infos = ['refDossier' => 'g46426', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $complement = $propriete->getComplement();

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'Htlouer';             // Nom du dossier
        $nameFile = 'g46426';               // Nom du Fichier sans extension
        $Rep = 'doc/report/Htlouer/';     // nom du répertoire final
        if(is_dir($Rep))
        {
            $this->directoryZip($Rep,$nameRep, $nameFile, $content, "g46426");
        }else{
            // Création du répertoire s'il n'existe pas.
            mkdir($Rep."/", 0775, true);
            $this->directoryZip($Rep,$nameRep, $nameFile, $content, "Htlouer");;
        }
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    public function ubiflow(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    ){
        $request = $this->requestStack->getCurrentRequest();
        //$partenaire = 'BI';
        $properties = $propertyRepository->reportpropertycsv2();            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);

            $dates = $this->propertyService->getDates($property);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $diffuseurs = [];
            if ($property['seloger'] == 1) {
                array_push($diffuseurs, 'MEILLEURSAGENTS');
            }
            if ($property['leboncoin'] == 1) {
                array_push($diffuseurs, 'LEBONCOIN_IMMO_V2');
            }
            if ($property['bienici'] == 1) {
                array_push($diffuseurs, 'INSOON_EB');
            }
            $publications = implode(",", $diffuseurs);
            // version du document
            $version = '4.11';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

            $infos = ['refDossier' => 'RC1860977', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Complements du bien
            $complement = $propriete->getOptions();

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'ubiflow';                           // Nom du dossier
        $nameFile = 'ubiflow';                          // Nom du Fichier sans extension
        $Rep = 'doc/report/ubiflow/';                   // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "ubiflow");
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme Superimmo - poliris 4.11
    public function clefsmoi(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'CM';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);

            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'SI';
            // version du document
            $version = '4.11';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

            $infos = ['refDossier' => 'papsimmo', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
            //dd($equipments);

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }

        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'lcdcm';                     // Nom du dossier
        $nameFile = 'paps_lcdcm';               // Nom du Fichier sans extension
        $Rep = 'doc/report/lcdcm/';             // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "lcdcm");
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme EtreProprio - version polaris à determiner
    public function etreproprio(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'EP';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);


            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'EP';
            // version du document
            $version = '4.10';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}
            $infos = ['refDossier' => 'g46426', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $complement = $propriete->getComplement();

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'EtreProprio';             // Nom du dossier
        $nameFile = 'ag166469';               // Nom du Fichier sans extension
        $Rep = 'doc/report/EProprio/';     // nom du répertoire final
        if(is_dir($Rep))
        {
            $this->directoryZip($Rep,$nameRep, $nameFile, $content, "ag166469");
        }else{
            // Création du répertoire s'il n'existe pas.
            mkdir($Rep."/", 0775, true);
            $this->directoryZip($Rep,$nameRep, $nameFile, $content, "EProprio");;
        }
        $this->generateExcel($properties, $Rep, $nameFile);
    }

    // Protocole de transfert des annonces pour la plateforme Superimmo - poliris 4.11
    public function monbien(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $partenaire = 'MB';
        $properties = $propertyRepository->reportpropertycsv4($partenaire);            // On récupère les biens à publier sur SeLoger

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
            $energies = $this->propertyService->getEnergies($propriete);
            // Description de l'annonce
            $annonce = $this->propertyService->getAnnonce($propriete);
            //dd($annonce);

            $dates = $this->propertyService->getDates($property);

            // Calcul des honoraires en %
            //$honoraires = round(100 - (($property['price'] * 100) / $property['priceFai']), 2);
            //dd($property['price'], $property['priceFai'], $honoraires);

            // Récupération des images liées au bien
            $url = $this->propertyService->getUrlPhotos($property);
            $titrephoto = $this->propertyService->getTitrePhotos($property);

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
            $publications = 'SI';
            // version du document
            $version = '4.11';

            // Transformation terrace en booléen
            if($property['terrace']){$terrace = 1;}else{$terrace = 0;}

            $infos = ['refDossier' => 'papsimmo', 'publications' => $publications, 'version' => $version, 'nord' => $nord, 'ouest' => $ouest, 'sud' => $sud, 'est' => $est, 'terrace' => $terrace];

            // Equipements
            $idcomplement = $property['idComplement'];
            $equipments = $complementRepository->findBy(['id'=> $idcomplement]);
            //dd($equipments);

            // Récupération DPE & GES
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);
            if($bilanGes > $bilanDpe){
                $bilanDpe = $bilanGes;
            }

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRow($propriete, $destination, $energies, $dates, $infos, $url, $titrephoto, $property, $version);
            $row = [];
            for ($i = 0; $i < count($data); $i++) {
                //dd($data[$i+1]);
                array_push($row, $data[$i+1]);
            }
            $rows[] = implode('!#', $row);
        }

        $content = implode("\n", $rows);

        // PARTIE II : Génération du dossier et création fichier CSV
        // ---------------------------------------------------------
        $nameRep = 'monbien';                     // Nom du dossier
        $nameFile = 'paps_monbien';               // Nom du Fichier sans extension
        $Rep = 'doc/report/monbien/';             // nom du répertoire final
        $this->directoryZip($Rep, $nameRep, $nameFile, $content, "monbien");
        $this->generateExcel($properties, $Rep, $nameFile);
    }
}