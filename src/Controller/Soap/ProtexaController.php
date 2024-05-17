<?php

namespace App\Controller\Soap;

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
                'ID' =>  $wsl['RESA'],
                'AGENCE' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE'],
                'DATE_DEBUT' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_DEBUT'],
                'DATE_FIN' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_FIN'],
                'TIER_NEGO' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['TIER_NEGO'],
                'NOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['NOM'],
                'DESCRIPTION_OBSERVATION' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS']['DESCRIPTION_OBSERVATION'],
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
            // detection des éléments
            if(!$wsl['MANDAT_SIGNE']){$id = null;} else {$id = $wsl['MANDAT_SIGNE'];};
            if(!$wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE']){$agence = null;} else {$agence = $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE'];};
            if(!$wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['ACTIONS']){$actions = '';} else {$actions = $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['ACTIONS'];};




            array_push($results, [
                // information sur le mandat
                'ID' =>  $id,
                'AGENCE' => $agence,
                'DATE_DEBUT' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_DEBUT'],
                'DATE_FIN' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_FIN'],
                'TIER_NEGO' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['TIER_NEGO'],
                // Informations sur le bien
                'FB_SURFACE_BIEN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_BIEN'],
                'FB_NB_PIECES' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_PIECES'],
                'FB_NB_CHAMBRES' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_NB_CHAMBRES'],
                'FB_TYPE_TERRAIN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_TYPE_TERRAIN'],
                'FB_SURFACE_TERRAIN' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_SURFACE_TERRAIN'],
                'FB_ORIGINE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_ORIGINE'],
                'FB_CARREZ' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_CARREZ'],
                'FB_OCCUPATION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_OCCUPATION'],
                'FB_DATE_LIBRE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FB_DATE_LIBRE'],
                'FM_PRIX' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_PRIX'],
                'FM_COM_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_COM_1'],
                'FM_COM_FIXE_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_COM_FIXE_1'],
                'FM_MT_COMMISSION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_MT_COMMISSION'],
                'FM_MT_COM_TOTALE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_MT_COM_TOTALE'],
                'FM_TYPE_COM' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_TYPE_COM'],
                'FM_NOTAIRE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['FM_NOTAIRE'],
                'RGPD_UTILISATION' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_UTILISATION'],
                'RGPD_1' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_1'],
                'RGPD_2' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_2'],
                'RGPD_3' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['RGPD_3'],
                'CONDPART' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['CONDPART'],
                'MOYENS' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['MOYENS'],
                'ACTIONS' => $actions,
                'MEDIATEUR' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['MEDIATEUR'],
                'PERIODICITE' => $wsdetailsmandat['MANDAT']['DONNEES']['FICHE_BIEN']['PERIODICITE'],
                // Informations sur le vendeurs
                'NOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['NOM'],
                'PRENOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PRENOM'],
                'ADRESSE_1' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['ADRESSE_1'],
                'ADRESSE_2' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['ADRESSE_2'],
                'CODE_POSTAL' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['CODE_POSTAL'],
                'VILLE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['VILLE'],
                'PAYS' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PAYS'],
                'DATE_NAISSANCE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['DATE_NAISSANCE'],
                'LIEU_NAISSANCE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['LIEU_NAISSANCE'],
                'PORTABLE' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['PORTABLE'],
                'EMAIL' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['EMAIL'],
                'LIEN' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['LIEN'],
                // Options complémentaires
                'DESCRIPTION_OBSERVATION' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS']['DESCRIPTION_OBSERVATION'],
            ]);
            //dd($wsdetailsmandat['MANDAT']['DONNEES']);
        }
        dd($results);
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
}
