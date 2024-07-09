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

        $rows = array();
        foreach ($properties as $property){
            $propriete = $propertyRepository->find($property['id']);
            //destination du bien
            $destination = $this->propertyService->getDestination($propriete);
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
            $bilanDpe = $this->propertyService->getClasseDpe($propriete);
            $bilanGes = $this->propertyService->getClasseGes($propriete);

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRowSLFIG($propriete, $destination, $dates, $infos, $url, $titrephoto, $property);
            $rows[] = implode('!#', $data);
        }
        $content = implode("\n", $rows);

        // PARTIE II : Génération du fichier CSV
        $file = 'doc/report/Annonces/Annonces.csv';                                // Chemin du fichier
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

            // Création d'une ligne du tableau
            $data = $this->propertyService->arrayRowSLFIG($propriete, $destination, $dates, $infos, $url, $titrephoto, $property);
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
    }

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
        $content = implode("\n", $adverts2);
        //dd($content);
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
    }
}