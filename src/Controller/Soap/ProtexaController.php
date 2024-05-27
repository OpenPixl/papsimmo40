<?php

namespace App\Controller\Soap;

use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\ProjectRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProtexaService;


class ProtexaController extends AbstractController
{
    #[Route('/soap/protexa', name: 'op_admin_soap_protexa_index')]
    public function index(): Response
    {
        return $this->render('soap/protexa/index.html.twig', [
            'controller_name' => 'protexaController',
        ]);
    }

    #[Route('/soap/protexa/registre', name: 'op_admin_soap_protexa_registre')]
    public function registre(ProtexaService $protexaService): Response
    {
        $results = [];
        $parameters = [
            'Login_connexion' => 'testclient@protexa.fr',
            'MotdePasse'=> 'VHWHZF'
        ];

        $client = $protexaService->getClient();
        $wslisteresa = $protexaService->callService($client, 'wslisteresa', $parameters);

        //dd($wslisteresa);
        foreach ($wslisteresa['RESERVATION_EN_COURS'] as $wsl){
            $parameters = [
                'Login' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $wsl['RESA']
            ];
            $wsdetailsmandat = $protexaService->callService($client, 'wsdetailsmandat', $parameters);
            //dd($wsdetailsmandat);
            array_push($results, [
                'STATUT' => "resaencours",
                'PARAMETRES' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES'],
                'FICHE_BIEN' => [],
                'MANDANT' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS'],
                'OBSERVATIONS' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS'],
            ]);
            //dd($wsdetailsmandat['MANDAT']['DONNEES']);
        }

        foreach ($wslisteresa['MANDAT_SIGNE_VIA_PROTEXA'] as $wsl){
            $parameters = [
                'Login' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $wsl['MANDAT_SIGNE']
            ];
            $wsdetailsmandat = $protexaService->callService($client, 'wsdetailsmandat', $parameters);
            //dd($wsdetailsmandat);

            array_push($results, [
                'STATUT' => "mandatsigne",
                // information sur le mandat
                'PARAMETRES' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES'],
                //'ID' =>  $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['ID_ORDR'],
                //'AGENCE' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE'],
                //'DATE_DEBUT' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_DEBUT'],
                //'DATE_FIN' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_FIN'],
                //'TIER_NEGO' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['TIER_NEGO'],
                // -----------   Informations sur le bien
                'FICHE_BIEN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN'],
                //'FB_SURFACE_BIEN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_BIEN'],
                //'FB_NB_PIECES' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_PIECES'],
                //'FB_NB_CHAMBRES' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_CHAMBRES'],
                //'FB_TYPE_TERRAIN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_TYPE_TERRAIN'],
                //'FB_SURFACE_TERRAIN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_TERRAIN'],
                //'FB_ORIGINE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_ORIGINE'],
                //'FB_CARREZ' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_CARREZ'],
                //'FB_OCCUPATION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_OCCUPATION'],
                //'FB_DATE_LIBRE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_DATE_LIBRE'],
                //'FM_PRIX' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_PRIX'],
                //'FM_COM_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_COM_1'],
                //'FM_COM_FIXE_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_COM_FIXE_1'],
                //'FM_MT_COMMISSION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_MT_COMMISSION'],
                //'FM_MT_COM_TOTALE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_MT_COM_TOTALE'],
                //'FM_TYPE_COM' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_TYPE_COM'],
                //'FM_NOTAIRE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_NOTAIRE'],
                //'RGPD_UTILISATION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_UTILISATION'],
                //'RGPD_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_1'],
                //'RGPD_2' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_2'],
                //'RGPD_3' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_3'],
                //'CONDPART' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['CONDPART'],
                //'MOYENS' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['MOYENS'],
                //'ACTIONS' => $actions,
                //'MEDIATEUR' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['MEDIATEUR'],
                //'PERIODICITE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['PERIODICITE'],
                // Informations sur le vendeurs
                'DESCRIPTION_MANDANT' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT'],
                //'NOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['NOM'],
                //'PRENOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PRENOM'],
                //'ADRESSE_1' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['ADRESSE_1'],
                //'ADRESSE_2' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['ADRESSE_2'],
                //'CODE_POSTAL' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['CODE_POSTAL'],
                //'VILLE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['VILLE'],
                //'PAYS' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PAYS'],
                //'DATE_NAISSANCE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['DATE_NAISSANCE'],
                //'LIEU_NAISSANCE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['LIEU_NAISSANCE'],
                //'PORTABLE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PORTABLE'],
                //'EMAIL' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['EMAIL'],
                //'LIEN' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['LIEN'],
                // Options complémentaires
                'OBSERVATIONS' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS'],
                //'DESCRIPTION_OBSERVATION' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS']['DESCRIPTION_OBSERVATION'],
            ]);
            //dd($wsdetailsmandat['MANDAT']['DONNEES']);
        }
        //dd($results);
        return $this->render('soap/protexa/registre.html.twig',[
            'results' => $results
        ]);
    }

    #[Route('/soap/protexa/{idmandat}/addmandant', name: 'op_admin_soap_protexa_addmandant')]
    public function addMandant($idmandat, ProtexaService $protexaService, Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->setAction($this->generateUrl('op_admin_soap_protexa_addmandant', ['idmandat' => $idmandat]))
            ->setMethod('POST')
            ->add('Mandat', TextType::class, [
                'label' => 'N° mandat'
            ])
            ->add('pMandant', TextType::class, [
                'label' => 'Nom et prénom'
            ])
            ->add('pAdresse', TextType::class, [
                'label' => 'Adresse'
            ])
            ->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = [
                'Login_connexion' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $idmandat,
                'pMandant' => $form->get('pMandant')->getData(),
                'pAdresse' => $form->get('pAdresse')->getData(),
            ];
            $client = $protexaService->getClient();
            $wsaddmandant = $protexaService->callService($client, 'wsaddmandant', $parameters);

            if (is_soap_fault($wsaddmandant)) {
                return $this->json([
                    'code' => 300,
                    'message' => 'Une erreur s\'est produite durant l\'opération.'
                ], 200);
            }

            return $this->json([
                'code' => 200,
                'message' => 'Ajout du mandat à la réservation validée auprès du fournisseur.'
            ], 200);
        }

        // view
        $view = $this->render('soap/protexa/_formAddMandant.html.twig', [
            'form' => $form,
        ]);
        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/soap/protexa/{idmandat}/addtypemandat', name: 'op_admin_soap_protexa_addtypemandat')]
    public function addtypemandat($idmandat, ProtexaService $protexaService, Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->setAction($this->generateUrl('op_admin_soap_protexa_addtypemandat', ['idmandat' => $idmandat]))
            ->setMethod('POST')
            ->add('Mandat', TextType::class, [
                'label' => 'N° mandat :'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de mandat :',
                'choices'  => [
                    'Baux-Commerciaux' => 'Baux-Commerciaux',
                    'Délégation/Substitution de mandat' => 'Délégation/Substitution de mandat',
                ],
                'choice_attr' => [
                    'Baux-Commerciaux' => ['data-data' => 'Baux-Commerciaux'],
                    'Délégation/Substitution de mandat' => ['data-data' => 'Délégation/Substitution de mandat'],
                ],
            ])
            ->add('sousType', TextType::class, [
                'label' => 'sous type de mandat :',
                'choices'  => [
                    'Droit au bail' => 'Droit au bail',
                    'Location avec droit d\'entrée' => 'Location avec droit d\'entrée',
                    'Délégation' => 'Délégation',
                ],
                'choice_attr' => [
                    'Droit au bail' => ['data-data' => 'Droit au bail'],
                    'Location avec droit d\'entrée' => ['data-data' => 'Location avec droit d\'entrée'],
                    'Délégation' => ['data-data' => 'Délégation']
                ],
            ])
            ->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = [
                'Login_connexion' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $idmandat,
                'type' => $form->get('type')->getData(),
                'sousType' => $form->get('sousType')->getData(),
            ];
            $client = $protexaService->getClient();
            $wsajouttype = $protexaService->callService($client, 'wsajouttype', $parameters);

            if (is_soap_fault($wsajouttype)) {
                return $this->json([
                    'code' => 300,
                    'message' => 'Une erreur s\'est produite durant l\'opération.'
                ], 200);
            }

            return $this->json([
                'code' => 200,
                'message' => 'Ajout du type à la réservation validée auprès du fournisseur.'
            ], 200);
        }

        // view
        $view = $this->render('soap/protexa/_formAddType.html.twig', [
            'form' => $form,
        ]);
        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/soap/protexa/{idmandat}/adddatemandat', name: 'op_admin_soap_protexa_adddatemandat')]
    public function adddatemandat($idmandat, ProtexaService $protexaService, Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->setAction($this->generateUrl('op_admin_soap_protexa_adddatemandat', ['idmandat' => $idmandat]))
            ->setMethod('POST')
            ->add('Mandat', TextType::class, [
                'label' => 'N° mandat'
            ])
            ->add('dateDebut', TextType::class, [
                'label' => 'Date de début'
            ])
            ->add('dureeInit', TextType::class, [
                'label' => 'Durée Initiale'
            ])
            ->add('dureeTR', TextType::class, [
                'label' => 'Durée Tacite Reconduction'
            ])
            ->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = [
                'Login_connexion' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $idmandat,
                'dateDebut' => $form->get('dateDebut')->getData(),
                'dureeInit' => $form->get('dureeInit')->getData(),
                'dureeTR' => $form->get('dureeTR')->getData(),
            ];
            $client = $protexaService->getClient();
            $wsajoutdates = $protexaService->callService($client, 'wsajoutdates', $parameters);

            if (is_soap_fault($wsajoutdates)) {
                return $this->json([
                    'code' => 300,
                    'message' => 'Une erreur s\'est produite durant l\'opération.'
                ], 200);
            }

            return $this->json([
                'code' => 200,
                'message' => 'Ajout du mandat à la réservation validée auprès du fournisseur.'
            ], 200);
        }

        // view
        $view = $this->render('soap/protexa/_formAddDates.html.twig', [
            'form' => $form,
        ]);
        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/soap/protexa/{idmandat}/addobservmandat', name: 'op_admin_soap_protexa_addobservmandat')]
    public function addobservmandat($idmandat, ProtexaService $protexaService, Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->setAction($this->generateUrl('op_admin_soap_protexa_addobservmandat', ['idmandat' => $idmandat]))
            ->setMethod('POST')
            ->add('Observation', TextType::class, [
                'label' => 'Observation'
            ])
            ->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = [
                'Login_connexion' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $idmandat
            ];
            $client = $protexaService->getClient();
            $wsaddobs = $protexaService->callService($client, 'wsaddobs', $parameters);

            if (is_soap_fault($wsaddobs)) {
                return $this->json([
                    'code' => 300,
                    'message' => 'Une erreur s\'est produite durant l\'opération.'
                ], 200);
            }

            return $this->json([
                'code' => 200,
                'message' => 'Ajout de l\'observation à la réservation validée auprès du fournisseur.'
            ], 200);
        }

        // view
        $view = $this->render('soap/protexa/_formAddObs.html.twig', [
            'form' => $form,
        ]);
        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/soap/protexa/{idmandat}/addproperty', name: 'op_admin_soap_protexa_addproperty')]
    public function addproperty(
        $idmandat,
        Request $request,
        PropertyRepository $propertyRepository,
        ProtexaService $protexaService,
        EmployedRepository $employedRepository,
        PublicationRepository $publicationRepository,
    ): Response
    {
        // Test des mandats présents sur l'application
        $mandats = $propertyRepository->listMandats();
        $arrayMandats = [];
        foreach ($mandats as $m){
            array_push($arrayMandats, $m['RefMandat']);
        }
        if(in_array($idmandat, $arrayMandats)){
            return $this->json([
                "code" => 300,
                'message' => 'Attention, le numéro de mandat présenté est présent sur PAPS\'s immo.'
            ], 200);
        }else{
            $parameters = [
                'Login' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $idmandat
            ];
            $client = $protexaService->getClient();
            $wsd = $protexaService->callService($client, 'wsdetailsmandat', $parameters);
            dd($wsd);

            // Récupération du collaborateur
            $user = $this->getUser();
            $employed = $employedRepository->find($user->getId());
            // Partie destinée à la table Property Publication
            $publication = new Publication();
            $publicationRepository->add($publication);

            // Partie destinée à la table Property
            $property = new Property();
            // ------ ADMIN -------
            $property->setRefEmployed($user);
            $property->setRefMandat($idmandat);
            $date = new \DateTime();
            $lastproperty = $propertyRepository->findOneBy([], ['id'=>'desc']);             // Récupération de la dernière propriété enregistrée
            if($lastproperty){
                $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
                $RefMandat = $refMandat;                           // construction du numéro de mandat obligatoire
            }else{
                $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
                $RefMandat = 22;
            }
            if(!$lastproperty){
                $lastRefNum = 1;
                $property->setRefnumdate($refNumDate);
                $property->setReflastnumber($lastRefNum);
            }else{
                $lastRefDate = $lastproperty->getRefnumdate();
                if($lastRefDate == $refNumDate){
                    $lastRefNum = $lastproperty->getReflastnumber()+1;
                    $property->setRefnumdate($refNumDate);
                    $property->setReflastnumber($lastRefNum);
                }else{
                    $lastRefNum = 1;
                    $property->setRefnumdate($refNumDate);
                    $property->setReflastnumber($lastRefNum);
                }
            }
            $property->setName('Mandat '.$idmandat.' signé sous Protexa.');

            // ------ ANNONCE -----
            $property->setAnnonce(
                '<p>'.$wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FB_DETAIL'].'</p>'.
                '<p>Contacter nous au : '.$user->getGsm().' ou '. $user->getEmail() .'</p><p>Les informations sur les risques auxquels, ce bien est exposé sont disponibles sur le site Géorisques : www.georisques.gouv.fr</p>'
            );
            $property->setPiece($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_PIECES']);
            $property->setRoom($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_CHAMBRES']);
            // ------ ADRESSE -------
            $property->setAdress($wsd['MANDAT']['DONNEES']['IMMEUBLES']['DESCRIPTION_IMMEUBLE']['IMMEUBLES_ADRESSE_1']);
            $property->setComplement($wsd['MANDAT']['DONNEES']['IMMEUBLES']['DESCRIPTION_IMMEUBLE']['IMMEUBLES_ADRESSE_1']);
            $property->setZipcode($wsd['MANDAT']['DONNEES']['IMMEUBLES']['DESCRIPTION_IMMEUBLE']['IMMEUBLES_ADRESSE_1']);
            $property->setCity($wsd['MANDAT']['DONNEES']['IMMEUBLES']['DESCRIPTION_IMMEUBLE']['IMMEUBLES_ADRESSE_1']);
            // ------ CHIFFRES -------
            $property->setPrice($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['250000']);
            $property->setHonoraires($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FM_MT_COM_TOTALE']);
            $property->setPriceFai($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['250000']);
            $property->setSurfaceHome($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_BIEN']);
            $property->setSurfaceLand($wsd['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_TERRAIN']);

            return $this->json([
                "code" => 200,
                'message' => 'Le mandat a été correctement ajouté à la liste des biens PAP\'s immo'
            ], 200);
        }
    }
}
