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
        $wslisteresaencours = $protexaService->callService($client, 'wslisteresaencours', $parameters);

        //dd($listews['RESERVATION_EN_COURS']);

        foreach ($wslisteresaencours['RESERVATION_EN_COURS'] as $wsl){
            $parameters = [
                'Login' => 'testclient@protexa.fr',
                'MotdePasse'=> 'VHWHZF',
                'Mandat' => $wsl['MANDAT']
            ];
            $wsdetailsmandat = $protexaService->callService($client, 'wsdetailsmandat', $parameters);
            array_push($results, [
                'ID' =>  $wsl['MANDAT'],
                'AGENCE' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['AGENCE'],
                'DATE_DEBUT' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_DEBUT'],
                'DATE_FIN' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['DATE_FIN'],
                'TIER_NEGO' => $wsdetailsmandat['MANDAT']['DONNEES']['PARAMETRES']['TIER_NEGO'],
                'NOM' => $wsdetailsmandat['MANDAT']['DONNEES']['MANDANTS']['DESCRIPTION_MANDANT']['NOM'],
                'DESCRIPTION_OBSERVATION' => $wsdetailsmandat['MANDAT']['DONNEES']['OBSERVATIONS']['DESCRIPTION_OBSERVATION'],
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
            $wsaddmandant = $protexaService->callService($client, 'wslisteresaencours', $parameters);

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
