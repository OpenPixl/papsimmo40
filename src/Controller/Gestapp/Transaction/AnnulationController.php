<?php

namespace App\Controller\Gestapp\Transaction;


use App\Controller\Gestapp\TransactionController;
use App\Entity\Admin\Application;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\Annulation;
use App\Form\Gestapp\Transaction\AnnulationType;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use App\Service\EmailService;
use App\Service\transactionService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

class AnnulationController extends AbstractController
{
    private bool $submit;
    private Application $application;

    public function __construct(
        public TransactionController $transactionController,
        public EmailService $emailService,
        public transactionService $transactionService,
        private readonly EntityManagerInterface $entityManager,
    ){
        $this->submit = true; // Initialisation de la variable $public
        $this->application = $entityManager->getRepository(Application::class)->find(1);
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    public function access(Transaction $transaction){
        $user = $this->getUser();
        $permission = 'read'; // Valeur par défaut
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $access = 'admin';
        } elseif ($this->isGranted('ROLE_EMPLOYED')) {
            if ($transaction->getRefEmployed() === $user) {
                $access = 'edit';
            } else {
                $access = 'read';
            }
        }
        return $access;
    }

    #[Route('/gestapp/transaction/annulation/{idTransaction}/open', name: 'op_gestapp_transaction_annulation_open', methods: ['GET','POST'])]
    public function open(
        Request $request,
        $idTransaction,
        TransactionRepository $transactionRepository,
        EntityManagerInterface $em,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger
    ): Response
    {
        $user = $this->getUser();

        $transaction = $transactionRepository->find($idTransaction);
        $access = $this->access($transaction);

        $annulation = new Annulation();
        $annulation->setTransaction($transaction);
        $form = $this->createForm(AnnulationType::class, $annulation, [
            'action' => $this->generateUrl('op_gestapp_transaction_annulation_open', [
                'idTransaction' => $idTransaction
            ]),
            'method' => 'POST',
            'attr' => [
                'id' => 'form_AnnulationTransaction'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            // les champs du formulaire sont valides
            if($form->isValid()) {
                // insertion du support de l'annulation
                // récupération de la référence du dossier pour construire le chemin vers le dossier Property
                $property = $propertyRepository->find($transaction->getProperty()->getId());
                $ref = explode("/", $property->getRef());
                $newref = $ref[0].'-'.$ref[1];
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";

                $supportFile = $form->get('supportFile')->getData();
                if($supportFile){
                    $newName = 'annulation-'.$property->getRefMandat().'-attestation_notaire.'.$supportFile->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        if (is_dir($pathdir)){
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $annulation->setSupportName($newName);
                }

                $annulation->setAuthor($user->getFirstName().' '.$user->getLastName());
                $em->persist($annulation);
                $em->flush();

                $transaction->setIsCancelled(true);
                $transaction->setStep(14);
                $property->setIsTransaction(0);
                $em->flush();

                $hasAccess = $this->isGranted('ROLE_ADMIN');
                if($hasAccess == true){
                    $transactions = $transactionRepository->findAll();
                }else{
                    $transactions = $transactionRepository->findBy(
                        ['refEmployed' => $user->getId()]
                    );
                }

                $transaction = $annulation->getTransaction();

                // Envoie d'un email au collaborateur ayant annulé la vente.
                if($this->submit === true && $access === 'edit'){
                    $this->emailService->submitEmailFromTransac(
                        $transaction->getRefEmployed()->getEmail(),
                        $this->getUser()->getFirstName()." ".$this->getUser()->getlastName()." de PAPs immo - ".$this->getUser()->getEmail(),
                        $this->application->getAdminEmail(),
                        '[SoftPAPS Transaction] - Annulation d\'une vente.',
                        $transaction->getId(),
                    );
                }
                // Envoie d'un email à l'admin d'une annulation de vente.
                if($this->submit == true && $access == "admin"){
                    $this->emailService->submitEmailFromTransac(
                        $this->application->getAdminEmail(),
                        'Administrateur SoftPAPs',
                        $transaction->getRefEmployed()->getEmail(),
                        '[SoftPAPS Transaction] - Annulation d\'une vente.',
                        $transaction->getId(),
                    );
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'l\'annulation de la vente est prise en compte dans le logiciel.',
                    'liste' => $this->renderView('gestapp/transaction/include/_liste.html.twig', [
                        'transactions' => $transactions,
                    ]),
                    'listecancelled' => $this->renderView('gestapp/transaction/include/_listecancelled.html.twig', [
                        'transactions' => $transactions,
                    ]),
                ], 200);
            }

            // Représentation du formulaire avec erreurs
            $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
                'annulation' => $annulation,
                'form' => $form
            ]);
            return $this->json([
                'code' => 422,
                'message' => 'Le formulaire présente une ou des erreurs.<br><span class="mt-1 mb-1 fw-semibold text-warning">'. implode(', ', $this->getFormErrors($form)). '</span><br>A vous de corriger celles-ci',
                'formView' => $view,
            ],200);
        }

        // Présentation du formulaire d'annulation vierge
        $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
            'annulation' => $annulation,
            'form' => $form
        ]);
        return $this->json([
            'code' => 200,
            'formView' => $view,
        ], 200);
    }

    #[Route('/gestapp/transaction/annulation/{id}/edit', name: 'op_gestapp_transaction_annulation_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Annulation $annulation, TransactionRepository $transactionRepository, EntityManagerInterface $em, PropertyRepository $propertyRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $transaction = $annulation->getTransaction();

        $form = $this->createForm(AnnulationType::class, $annulation, [
            'action' => $this->generateUrl('op_gestapp_transaction_annulation_edit', [
                'id' => $annulation->getId()
            ]),
            'method' => 'POST',
            'attr' => [
                'id' => 'form_AnnulationTransaction'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            // les champs du formulaire sont valides
            if($form->isValid()) {
                // insertion du support de l'annulation
                // récupération de la référence du dossier pour construire le chemin vers le dossier Property
                $property = $propertyRepository->find($transaction->getProperty()->getId());
                $ref = explode("/", $property->getRef());
                $newref = $ref[0].'-'.$ref[1];
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";

                $supportFile = $form->get('supportFile')->getData();
                if($supportFile){
                    // Ajout de la nouvelle photo
                    $originalName = pathinfo($supportFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeName = 'ann-'.$slugger->slug($originalName);
                    $newName = $safeName . $supportFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        if (is_dir($pathdir)){
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $annulation->setSupportName($newName);
                }

                $supportFact = $form->get('supportFact')->getData();
                if($supportFact){
                    // Ajout de la nouvelle photo
                    $originalName = pathinfo($supportFact->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeName = 'annfact-'.$slugger->slug($originalName);
                    $newName = $safeName . $supportFact->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        if (is_dir($pathdir)){
                            $supportFact->move(
                                $pathdir,
                                $newName
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            $supportFact->move(
                                $pathdir,
                                $newName
                            );
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $annulation->setSupportFact($newName);
                }

                $supportFactColl = $form->get('supportFactColl')->getData();
                if($supportFactColl){
                    // Ajout de la nouvelle photo
                    $originalName = pathinfo($supportFactColl->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeName = 'annfactcoll-'.$slugger->slug($originalName);
                    $newName = $safeName . $supportFactColl->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        if (is_dir($pathdir)){
                            $supportFactColl->move(
                                $pathdir,
                                $newName
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            $supportFactColl->move(
                                $pathdir,
                                $newName
                            );
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $annulation->setSupportFactColl($newName);
                }

                $annulation->setAuthor($user->getFirstName().' '.$user->getLastName());

                $transaction->setIsCancelled(true);
                $property->setIsTransaction(0);

                $hasAccess = $this->isGranted('ROLE_ADMIN');
                if($hasAccess == true){
                    $transactions = $transactionRepository->findAll();
                }else{
                    $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
                }

                $em->persist($annulation);
                $em->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'l\'annulation de la vente est prise en compte dans le logiciel.',
                    'liste' => $this->renderView('gestapp/transaction/include/all_list_transactions.html.twig', [
                        'transactions' => $transactions,
                    ]),
                ], 200);
            }

            // Représentation du formulaire avec erreurs
            $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
                'annulation' => $annulation,
                'form' => $form
            ]);
            return $this->json([
                'code' => 422,
                'message' => 'Le formulaire présente une ou des erreurs.<br><span class="mt-1 mb-1 fw-semibold text-warning">'. implode(', ', $this->getFormErrors($form)). '</span><br>A vous de corriger celles-ci',
                'formView' => $view,
            ],200);
        }

        // Présentation du formulaire d'annulation vierge
        $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
            'annulation' => $annulation,
            'form' => $form
        ]);
        return $this->json([
            'code' => 200,
            'formView' => $view,
        ], 200);
    }

    #[Route('/gestapp/transaction/annulation/{id}/newfile', name: 'op_gestapp_transaction_annulation_newfile', methods: ['GET','POST'])]
    public function newFile(Annulation $annulation, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PropertyRepository $propertyRepository): Response
    {
        $user = $this->getUser();
        $transaction = $annulation->getTransaction();
        $access = $this->access($transaction);

        if($annulation->getSupportName() == null && $annulation->getSupportFact() == null && $annulation->getSupportFactColl() == null){
            $label = 'Attestation d\'annulation du notaire';
            $doctype= 'attestation_notaire';
        }else if ($annulation->getSupportName() !== null && $annulation->getSupportFact() == null && $annulation->getSupportFactColl() == null){
            $label = 'Attestation d\'annulation du notaire';
            $doctype= 'facture_client';
        }else if ($annulation->getSupportName() !== null && $annulation->getSupportFact() == !null && $annulation->getSupportFactColl() == null){
            $label = 'Attestation d\'annulation du notaire';
            $doctype = 'facture_collaborateur';
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_annulation_newfile', ['id' => $annulation->getId()]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formDocsCancelled_add',
                ],
            ])
            ->add('docAnnulation', FileType::class,[
                'label' => $label,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '40952k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if($form->isValid()){
                $docFile = $form->get('docAnnulation')->getData();

                $property = $propertyRepository->find($transaction->getProperty()->getId());
                $ref = explode("/", $property->getRef());
                $newref = $ref[0].'-'.$ref[1];
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";

                if($doctype == 'attestation_notaire'){
                    if($docFile){
                        $newName = 'annulation-'.$property->getRefMandat().'-attestation_notaire.'.$docFile->guessExtension();
                        try {
                            if (is_dir($pathdir)){
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }else{
                                // Création du répertoire s'il n'existe pas.
                                mkdir($pathdir."/", 0775, true);
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        $annulation->setSupportName($newName);
                    }
                }
                if($doctype == 'facture_client'){
                    if($docFile){
                        $newName = 'annulation-'.$property->getRefMandat().'-facture_client.'.$docFile->guessExtension();
                        try {
                            if (is_dir($pathdir)){
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }else{
                                // Création du répertoire s'il n'existe pas.
                                mkdir($pathdir."/", 0775, true);
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        $annulation->setSupportFact($newName);
                        $annulation->setIsValidFactAdmin(1);
                    }
                }
                if($doctype == 'facture_collaborateur'){
                    if($docFile){
                        $newName = 'annulation-'.$property->getRefMandat().'-facture_collaborateur.'.$docFile->guessExtension();
                        try {
                            if (is_dir($pathdir)){
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }else{
                                // Création du répertoire s'il n'existe pas.
                                mkdir($pathdir."/", 0775, true);
                                $docFile->move(
                                    $pathdir,
                                    $newName
                                );
                            }
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        $annulation->setSupportFactColl($newName);
                    }
                }

                $annulation->setAuthor($user->getFirstName().' '.$user->getLastName());

                $em->persist($annulation);
                $em->flush();

                $this->transactionService->step($transaction);

                if ($doctype == 'facture_annulation') {
                    $this->emailService->submitEmailFromTransac(
                        $this->application->getAdminEmail(),
                        'Administrateur SoftPAPs',
                        $transaction->getRefEmployed()->getEmail(),
                        '[SoftPAPS Transaction ] - Annulation d\'une vente.',
                        $transaction->getId(),
                    );
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'l\'annulation de la vente est prise en compte dans le logiciel.',
                    'view' => $this->renderView('gestapp/transaction/show/_docscancelled.html.twig', [
                        'transaction' => $transaction,
                        'access' => $access
                    ])
                ], 200);
            }
            // Représentation du formulaire avec erreurs
            $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
                'annulation' => $annulation,
                'form' => $form
            ]);
            return $this->json([
                'code' => 422,
                'message' => 'Le formulaire présente une ou des erreurs.<br><span class="mt-1 mb-1 fw-semibold text-warning">'. implode(', ', $this->getFormErrors($form)). '</span><br>A vous de corriger celles-ci',
                'formView' => $view,
            ],200);
        }

        return $this->json([
            'code'=> 200,
            'formView' => $this->renderView('gestapp/transaction/annulation/_formDocs.html.twig', [
                'annulation' => $annulation,
                'form' => $form
            ])
        ], 200);
    }

    #[Route('/gestapp/transaction/annulation/{id}/valid/{file}', name: 'op_gestapp_transaction_annulation_validfiles')]
    public function validFiles(
        Annulation $annulation,
        $file,
        PropertyRepository $propertyRepository,
        Request $request,
        EntityManagerInterface $em,
        transactionService $transactionService,
    )
    {
        $transaction = $annulation->getTransaction();
        $access = $this->access($transaction);

        $data = json_decode($request->getContent(), true);
        $isValid = $data['option'] ?? null;
        $messageInvalid=$data['message'] ?? null;
        //$typeDoc = explode('-', $file)[0];
        $message = '';
        $view = '';
        if($file == 'Annulation'){
            $fileName = $annulation->getSupportName();
        }else if($file == 'AnnulationFacture'){
            $fileName = $annulation->getSupportFact();
        }else if($file == 'AnnulationFactureCollaborateur'){
            $fileName = $annulation->getSupportFactColl();
        }
        
        if($isValid == 'validFileCancelled'){
            if($file == 'Annulation'){
                $annulation->setIsValidNotarialDoc(1);
                $message = "Vous venez de valider l'attestation liée à l'annulation cette vente. <br>
                          Un mail va être adressé au collaborateur afin de lui confirmer l'annulation.";
            }
            if($file == 'AnnulationFacture'){
                $annulation->setIsValidFactAdmin(1);
                $message = "Vous venez d'ajouter la facture client dédiée a cette annulation. <br>
                          Un mail va être adressé au collaborateur pour qu'il puisse déposer sa facture.";
            }
            if($file == 'AnnulationFactureCollaborateur'){
                $annulation->setIsValidFactColl(1);
                $message = "Vous venez de valider la facture de votre collaborateur. <br>
                          un mail va être adressé au collaborateur pour lui signifier la validité de la facture.";
            }

            $em->flush();
            $this->transactionController->step($transaction);

            // Envoie d'un email à l'admin d'une annulation de vente.
            if($this->submit == true && $access == "editor"){
                $this->emailService->submitEmailFromTransac(
                    $transaction->getRefEmployed()->getEmail(),
                    'Collaborateur SoftPAPs : ' . $transaction->getRefEmployed()->getFirstName() . ' ' . $transaction->getRefEmployed()->getLastName(),
                    $this->application->getAdminEmail(),
                    '[SoftPAPS Transaction ] - Annulation d\'une vente.',
                    $transaction->getId(),
                );
            }
            // Envoie d'un email à l'admin d'une annulation de vente.
            if($this->submit == true && $access == "admin"){
                $this->emailService->submitEmailFromTransac(
                    $this->application->getAdminEmail(),
                    'Administrateur SoftPAPs',
                    $transaction->getRefEmployed()->getEmail(),
                    '[SoftPAPS Transaction ] - Annulation d\'une vente.',
                    $transaction->getId(),
                );
            }

            return $this->json([
                "code" => 200,
                "message" => $message,
                "view" => $this->renderView('gestapp/transaction/show/_docscancelled.html.twig', [
                    'transaction' => $transaction,
                    'access' => $access
                ])
            ], 200 );
        }
        elseif ($isValid == 'invalidFile'){

            return $this->json([
                "code" => 200,
                "message" => $messageInvalid,
                "view" => $this->renderView('gestapp/transaction/show/_docscancelled.html.twig', [
                    'Transaction' => $transaction,
                    'access' => $access
                ])
            ], 200 );
        }

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];
        $pathdir = "/properties/".$newref."/documents/".$fileName;

        return $this->json([
            'code'=> 200,
            'path' => $pathdir,
        ], 200);

    }

    #[Route('/gestapp/transaction/annulation/{id}/delfile/{file}', name: 'op_gestapp_transaction_annulation_delfile', methods: ['POST'])]
    public function delFile(
        Annulation $annulation,
        EntityManagerInterface $em,
        $file,
        PropertyRepository $propertyRepository
    ): Response{
        $transaction = $annulation->getTransaction();
        $access = $this->access($transaction);

        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        if($file == 'Annulation'){
            $fileName = $annulation->getSupportName();
            $pathfile = $this->getParameter('property_doc_directory').$newref."/documents/".$fileName;
            if(file_exists($pathfile)){
                unlink($pathfile);
            }
            $annulation->setSupportName(null);
            $annulation->setIsValidNotarialDoc(0);
        }elseif($file == 'AnnulationFacture'){
            $fileName = $annulation->getSupportFact();
            $pathfile = $this->getParameter('property_doc_directory').$newref."/documents/".$fileName;
            if(file_exists($pathfile)){
                unlink($pathfile);
            }
            $annulation->setSupportFact(null);
            $annulation->setIsValidFactAdmin(0);
        }elseif($file == 'AnnulationFactureCollaborateur'){
            $fileName = $annulation->getSupportFactColl();
            $pathfile = $this->getParameter('property_doc_directory').$newref."/documents/".$fileName;
            if(file_exists($pathfile)){
                unlink($pathfile);
            }
            $annulation->setSupportFactColl(null);
            $annulation->setIsValidFactColl(0);
        }

        $em->flush();
        $this->transactionController->step($transaction);

        return $this->json([
            'code'=> 200,
            'view' => $this->renderView('gestapp/transaction/show/_docscancelled.html.twig', [
                'transaction' => $transaction,
                'access' => $access
            ]),
        ], 200);
    }
}
