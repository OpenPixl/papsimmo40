<?php

namespace App\Controller\Gestapp;

use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    #[Route('/gestapp/report', name: 'app_gestapp_report')]
    public function index(): Response
    {
        return $this->render('gestapp/report/index.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }

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
            //dd($annonce);
            //dd($property);
            if ($property['dpeAt'] && $property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
            }else{
                $dpeAt ="";
            }
            // dd($dpeAt);
            // Clé de détermination PARUVENDU - FAMILLE
            if($property['projet']){
                $famille = $property['projet'];
            }else{
                $famille = "";
            }
            // Clé de détermination PARUVENDU - RUBRIQUE
            if($property['propertyDefinition']){
                $rubrique = $property['propertyDefinition'];
            }else{
                $rubrique = "00";
            }
            // Clé de détermination PARUVENDU - SSRUBRIQUE
            if($property['ssCategory']){
                $ssrubrique = $property['ssCategory'];
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
                '"'.$property['ref'].'"',                               // 2 - Référence ANNONCE du PAPSIMMO
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
                '""',                                                   // 49 - Honoraire à la charge de l'acquéreur
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
}
