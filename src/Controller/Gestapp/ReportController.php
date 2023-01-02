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
        //dd($app);

        $rows = array();
        foreach ($properties as $property){
            if ($property['dpeAt'] instanceof \DateTime) {
                $dpeAt = $property['dpeAt']->format('d/m/Y');
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
                        ${'url'.$key+1} = 'https://'.$app.'/media/cache/resolve/lastproperty_card/images/galery/'.$photos[$key]['galeryFrontName'];
                        array_push($url, ${'url'.$key+1});
                    }else{
                        ${'url'.$key+1} = '';
                        array_push($url, ${'url'.$key+1});
                    }
                }
            }

            //dd($url);
            $data = array(
                'CodeClient',                               // 1 - code Client fournis par PV
                '"'.$property['ref'].'"',                               // 2 - Référence ANNONCE du PAPSIMMO
                'I',                                        // 3 - Code Pour les biens immobiliers correspondance PV
                'famille',                                  // 4
                'rubrique',                                 // 5
                'sous-rubrique',                            // 6
                '""',                                         // 7 - code INSEE COMMUNE
                '"'.$property['zipcode'].'"',                       // 8
                '"'.$property['city'].'"',                          // 9
                'France',                                   // 10
                '"'.$property['name'].'"',                          // 11
                '""',                                         // 12
                '"'.$property['gsm'].'"',                           // 13
                '"'.$property['email'].'"',                         // 14
                'fax',                                      // 15
                '"'.$url1.'"',                               // 16 - Chemin de la 1ère photo
                '"'.$url2.'"',                               // 17 - Chemin de la 2de photo
                '"'.$url3.'"',                               // 18 - Chemin de la 3ème photo
                '"'.$url4.'"',                               // 19 - Chemin de la 4ème photo
                '"'.$url5.'"',                               // 20 - Chemin de la 5ème photo
                '"'.$url6.'"',                               // 21 - Chemin de la 6ème photo
                '"'.$property['priceFai'].'"',                     // 22
                '"0"',                                          // 23 - Loyer Charges comprises
                '"0"',                                          // 24 - Loyer sans charges
                '"0"',                                          // 25 - Charges
                '"0"',                                          // 26 - Honoraires Charges Locataires
                '"0"',                                          // 27 - A ajouter dans la BDD - Terrain ou bien Constructible
                '"'.$property['surfaceHome'].'"',                   // 28
                '"'.$property['surfaceLand'].'"',                   // 29
                '""',                                         // 30 - Nom du Quartier
                '"'.$property['isFurnished'].'"',                   // 31
                '"'.$property['piece'].'"',                         // 32 - Nombre de pièces
                '""',                                         // 33 - Url de visite virtuelle
                '""',                                         // 34 - Texte supplémentaire
                '""',                                         // 35 - Programme immo neuf
                '"'.$property['level'].'"',                         // 36
                '""',                                         // 37 - Lien contact - Programme imm neuf
                "1",                                          // 38 - Mettre en ligne le bien - PV
                '""',                                         // 39 - Ancienneté
                '"'.$property['constructionAt'].'"',                // 40 - Année de construction
                '""',                                         // 41 - Dépot de garantie
                '"'.$property['room'].'"',                          // 42 - Nombre de chambres
                '"'.$property['bathroom'].'"',                      // 43 -
                '""',                                         // 44 - Nombre de parking extérieur
                '""',                                         // 45 - Nombre de parking intérieur
                '"'.$property['diagDpe'].'"',                       // 46
                '"'.$property['diagGes'].'"',                       // 47
                '"'.$property['isWithExclusivity'].'"',             // 48
                '""',                                         // 49 - Honoraire à la charge de l'acquéreur
                '""',                                         // 50 - Pourcentage de honoraires à la charge de l'acquéreur
                '"'.$property['coproperty'].'"',                    // 51
                '""',                                         // 52 - Nombre de lots
                0,                                          // 53 - Montant moyen des charges annuelles
                '""',                                         // 54 - procédure sur le syndicat des copropriétaires
                '"'.$url7.'"',                                         // 56 - url photo 7
                '"'.$url8.'"',                                         // 57 - url photo 8
                '"'.$url9.'"',                                         // 58 - url photo 9
                '""',                                         // 59 - Modalité Règlement charges - Location
                '""',                                         // 60 - Complement de loyer
                '""',                                         // 61 - Dépôt de garantie
                '""',                                         // 62
                '"'.$property['price'].'"',                         // 63 -
                '""',                                         // 64 - url Baremes Honoraires
                '"'.$url10.'"',                                         // 65 - url photo 10
                '"'.$url11.'"',                                         // 66 - url photo 11
                '"'.$url12.'"',                                         // 67 - url photo 12
                '"'.$url13.'"',                                         // 68 - url photo 13
                '"'.$url14.'"',                                         // 69 - url photo 14
                '"'.$url15.'"',                                         // 70 - url photo 15
                '"'.$dpeAt.'"',                         // 71
                '"'.$property['dpeEstimateEnergyDown'].'"',         // 72
                '"'.$property['dpeEstimateEnergyUp'].'"',           // 73
            );
            $rows[] = implode('|', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}
