<?php

namespace App\Controller\Gestapp;

use App\Entity\Admin\Application;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\CustomerType;
use App\Form\Gestapp\TransactionActedateType;
use App\Form\Gestapp\TransactionActepdfType;
use App\Form\Gestapp\TransactionHonorairesType;
use App\Form\Gestapp\TransactionInvoicepdfType;
use App\Form\Gestapp\TransactionTracfinpdfType;
use App\Form\Gestapp\TransactionType;
use App\Form\Gestapp\Transactionstep2Type;
use App\Form\Gestapp\Transactionstep3Type;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\CustomerChoiceRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use App\Service\EmailService;
use App\Service\NotificationService;
use App\Service\transactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\File;

#[Route('/gestapp/transaction')]
class TransactionController extends AbstractController
{
    private bool $submit;
    private Application $application;

    public function __construct(
        public NotificationService $notificationService,
        public EmailService        $emailService,
        public TransactionService  $transactionService,
        public SluggerInterface    $slugger,
        private readonly EntityManagerInterface $entityManager,
        public PhotoRepository $photoRepository,
    )
    {
        $this->submit = true; // Initialisation de la variable $public
        $this->application = $entityManager->getRepository(Application::class)->find(1);
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

    private function returnView(Transaction $transaction, $access, $path){

        $property = $transaction->getProperty();
        $photo = $this->photoRepository->firstphoto($property->getId());

        return [
            'view' => $this->renderView($path, [
                'transaction' => $transaction,
                'access' => $access
            ]),
            'state' => $this->renderView('gestapp/transaction/show/_stateTransaction.html.twig', [
                'transaction' => $transaction,
            ]),
            'progress' => $this->renderView('gestapp/transaction/show/_cardInformations.html.twig', [
                'transaction' => $transaction,
                'photo' => $photo,
                'access' => $access
            ]),
            'actionButtons' => $this->renderView('gestapp/transaction/show/_actionButtons.html.twig', [
                'transaction' => $transaction,
                'access' => $access
            ]),
        ];
    }

    #[Route('/{id}/step', name: 'op_gestapp_transaction_step', methods: ['GET'])]
    public function step(Transaction $transaction){
        //dd($transaction->getCustomer()->count() > 0);
        if(!$transaction->getCustomer()->count() > 0) {
            $transaction->setState('Ouverture du dossier | En attente d\'un ou de plusieurs acquéreurs');
            $transaction->setStep(0);
            $this->entityManager->flush();
            return 0;
        }
        if(!$transaction->getDateAtPromise()){
            $transaction->setState('Promesse de vente | En attente de la date du RDV');
            $transaction->setStep(1);
            $this->entityManager->flush();
            return 1;
        }
        if(!$transaction->getPromisePdfFilename()){
            $transaction->setState('Promesse de vente | En attente du chargement du fichier Pdf');
            $transaction->setStep(2);
            $this->entityManager->flush();
            return 2;
        }
        if(!$transaction->isIsValidPromisepdf()){
            $transaction->setState('Promesse de vente | En attente de la validation du pdf par l\'administrateur');
            $transaction->setStep(3);
            $this->entityManager->flush();
            return 3;
        }
        if(!$transaction->getHonorairesPdfFilename()){
            $transaction->setState('Honoraires | En attente du chargement du fichier Pdf');
            $transaction->setStep(4);
            $this->entityManager->flush();
            return 4;
        }
        if(!$transaction->isIsValidHonoraires()){
            $transaction->setState('Honoraires | En attente de la validation du pdf par l\'administrateur');
            $transaction->setStep(5);
            $this->entityManager->flush();
            return 5;
        }
        if(!$transaction->getDateAtSale()){
            $transaction->setState('Acte de vente ou Tracfin | En attente de la date du RDV');
            $transaction->setStep(6);
            $this->entityManager->flush();
            return 6;
        }
        if(!$transaction->getActePdfFilename() || !$transaction->getTracfinPdfFilename()){
            $transaction->setState('Acte de vente ou Tracfin | En attente du chargement du fichier Pdf');
            $transaction->setStep(7);
            $this->entityManager->flush();
            return 7;
        }
        if((!$transaction->isIsValidActepdf() || $transaction->isIsValidActepdf() == 0) || (!$transaction->isIsValidtracfinPdf() || $transaction->isIsValidtracfinPdf() == 0)){
            $transaction->setState('Acte de vente ou Tracfin | En attente de la validation du pdf par l\'administrateur');
            $transaction->setStep(8);
            $this->entityManager->flush();
            return 8;
        }
        if(!$transaction->getInvoicePdfFilename()){
            $transaction->setState('Facture de vente | En attente du chargement du fichier Pdf');
            $transaction->setStep(9);
            $this->entityManager->flush();
            return 9;
        }
        if(!$transaction->isIsValidInvoicePdf()){
            $transaction->setState('Facture de vente | Validée par l\'administrateur');
            $transaction->setStep(10);
            $this->entityManager->flush();
            return 10;
        }
        if($transaction->isIsValidInvoicePdf() == 1){
            $transaction->setState('Facture de vente | Validée par l\'administrateur');
            $transaction->setStep(11);
            $this->entityManager->flush();
            return 11;
        }
        if($transaction->getStep() == 11 && $transaction->getAddCollTransacs()->count() > 0){
            $transaction->setState('Autres Factures | Factures de collaborateur');
            $transaction->setStep(12);
            $this->entityManager->flush();
            return 12;
        }

        $step = $transaction->getStep();

        return $step;
    }

    #[Route('/', name: 'op_gestapp_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        if($hasAccess == true){
            $transactions = $transactionRepository->findAll();
        }else{
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
        }

        return $this->render('gestapp/transaction/index.html.twig', [
            'transactions' => $transactions,
            'user' => $user
        ]);
    }

    #[Route('/updateprogress', name: 'op_gestapp_transaction_updateprogress', methods: ['GET'])]
    public function updateProgress(TransactionRepository $transactionRepository, EntityManagerInterface $em, transactionService $transactionService)
    {
        $transactions = $transactionRepository->findAll();
        foreach($transactions as $transaction)
        {
            $project = $transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();
        }

        return $this->redirectToRoute('op_gestapp_transaction_index');
    }

    #[Route('/new/{idproperty}', name: 'op_gestapp_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, $idproperty, EntityManagerInterface $entityManager, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->find($idproperty);
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/transaction/new.html.twig', [
            'property' => $property,
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // $document : fichier transmis par l'input
    // $suffixe = suffixe a donner pour le nnouveau nom de fichier
    // $pathdir = chemin du fichier
    // $pdfName = nom du fichier si ce dernier est présent dans l'entité,
    public function addFiles($document, $suffixe, $pathdir, $pdfName){
        $pathfile = $pathdir.$pdfName;
        // Si Fichier présent Suppression
        if($pdfName){
            // On vérifie si l'image existe
            if(file_exists($pathfile)){
                unlink($pathfile);
            }
        }
        $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $suffixe.$safeFilename.'.'.$document->guessExtension();
        try {
            if (is_dir($pathdir)){
                $document->move(
                    $pathdir,
                    $newFilename
                );
            }else{
                // Création du répertoire s'il n'existe pas.
                mkdir($pathdir."/", 0775, true);
                // Déplacement de la photo
                $document->move(
                    $pathdir,
                    $newFilename
                );
            }
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $newFilename;
    }

    /**
     * Adds a new transaction for a given property.
     *
     * This method creates a transaction for a specified property, provided
     * the property is not already part of an ongoing transaction. It updates
     * the property's transaction status, creates a corresponding transaction
     * entity, and sends a notification email to administrative contacts.
     *
     * If the property is already in a transaction, it redirects the user to
     * the transaction index without creating another transaction.
     *
     * @param Request $request Information about the current HTTP request.
     * @param int $idproperty The identifier of the property for which the transaction is being created.
     * @param EntityManagerInterface $entityManager Handles database operations for persisting and managing entities.
     * @param PropertyRepository $propertyRepository Repository for interacting with the "Property" entity in the database.
     * @param MailerInterface $mailer Service that facilitates sending emails.
     *
     * @return Response Redirects the user to either the transaction index if the property is already in a transaction,
     *                   or to a page showing details of the newly created transaction.
     */
    #[Route('/add/{idproperty}', name: 'op_gestapp_transaction_add', methods: ['GET'])]
    public function add(
        Request $request,
        $idproperty,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        MailerInterface $mailer,
    )
    {
        $user = $this->getUser();
        $property = $propertyRepository->find($idproperty);
        $isTransaction = $property->isIsTransaction();
        $id = $property->getId();
        if($isTransaction == true){
            return $this->redirectToRoute('op_gestapp_transaction_index', [], Response::HTTP_SEE_OTHER);
            // mettre en flash que le bien est déjà en, cours de transaction.
        }
        $name = 'trans-'.$property->getRef();
        $transaction = new Transaction();
        $transaction->setProperty($property);
        $transaction->setState('Ouverture du dossier | En attente d\'un ou de plusieurs acquéreurs');
        $transaction->setProject(0);
        $transaction->setName($name);
        $transaction->setRefEmployed($user);
        $entityManager->persist($transaction);
        $property->setIsTransaction(1);
        $entityManager->persist($property);
        $entityManager->flush();

        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'))
            ->to($this->application->getAdminEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('[PAPs immo] : Une nouvelle transaction immobilière est engagée')
            ->htmlTemplate('admin/mail/messageNewTransaction.html.twig')
            ->context([
                'transaction' => $transaction,
                'ref' => $newref,
                'url' => $request->server->get('HTTP_HOST')
            ]);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            dd($e);
        }

        return $this->redirectToRoute('op_gestapp_transaction_show2', [
            'id' => $transaction->getId()
        ]);
    }

    #[Route('/{id}/add_appointment', name: 'op_gestapp_transaction_addappointment', methods: ['GET', 'POST'])]
    public function addAppointments(Request $request, Transaction $transaction, EntityManagerInterface $em): Response
    {
        $access = $this->access($transaction);

        $dateAtPromise = $transaction->getDateAtPromise();
        $dateAtActe = $transaction->getDateAtSale();

        if ($dateAtPromise !== null && $dateAtActe !== null){
            return $this->json([
                'code'=> 400,
                'formView' => 'impossible d\'ajouter une date à ce dossier.'
            ], 400);
        }
        if ($dateAtPromise == null && $dateAtActe == null){
            $label = 'Date de la prommesse de vente';
        } elseif ($dateAtPromise !== null && $dateAtActe == null){
            $label = 'Date de l\'acte de vente';
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_addappointment', ['id' => $transaction->getId()]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formAppointment_add',
                ],
            ])
            ->add('appointment', DateType::class, [
                'label' => $label,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données sous forme de tableau associatif
            $date = $form->get('appointment')->getData();
            if ($transaction->getDateAtPromise() === null) {
                $transaction->setDateAtPromise($date);
            } elseif ($transaction->getDateAtSale() === null) {
                $transaction->setDateAtSale($date);
            } else {
                return $this->json([
                    'code'=> 400,
                    'message' => "impossibles d'ajouter une date à ce dossier.",
                ], 400);
            }

            $em->flush();

            $project = $this->transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            $this->step($transaction);

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
            ], $this->returnView($transaction, $access, 'gestapp/transaction/show/_appointment.html.twig')), 200);
        }

        $view = $this->render('gestapp/transaction/show/_appointmentForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);
    }

    #[Route('/{id}/{appointment}/edit_appointment', name: 'op_gestapp_transaction_editappointment', methods: ['GET', 'POST'])]
    public function editAppointments(Request $request, Transaction $transaction, $appointment,EntityManagerInterface $em): Response
    {
        $access = $this->access($transaction);

        if($appointment == 'dateAtPromise'){
            $label = 'Date de la prommesse de vente';
            $value = $transaction->getDateAtPromise();
        }else if($appointment == 'dateAtActe') {
            $label = 'Date de l\acte de vente';
            $value = $transaction->getDateAtSale();
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_editappointment', [
                    'id' => $transaction->getId(),
                    'appointment' => $appointment
                ]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formAppointment_edit',
                ],
            ])
            ->add('appointment', DateType::class, [
                'label' => $label,
                'data' => $value, // Valeur initiale issue de l'entité
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                'required' => false,
                'by_reference' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données sous forme de tableau associatif
            $date = $form->get('appointment')->getData();

            if($appointment === 'dateAtPromise'){
                $transaction->setDateAtPromise($date);
            }else if($appointment == 'dateAtActe') {
                $transaction->setDateAtSale($date);
            }

            $em->flush();

            $project = $this->transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
            ],$this->returnView($transaction, $access, 'gestapp/transaction/show/_appointment.html.twig')), 200);
        }

        $view = $this->render('gestapp/transaction/show/_appointmentForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);
    }

    #[Route('/{id}/add_documents', name: 'op_gestapp_transaction_adddocuments', methods: ['GET', 'POST'])]
    public function addDocuments(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $em,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger,
        transactionService $transactionService
    ): Response
    {
        $access = $this->access($transaction);

        $pdfPromise = $transaction->getPromisePdfFilename();
        $validPromise = $transaction->isIsValidPromisepdf();
        $pdfActe = $transaction->getActePdfFilename();
        $validActe = $transaction->isIsValidActepdf();
        $pdfTracfin = $transaction->getTracfinPdfFilename();
        $validTracfin = $transaction->isIsValidtracfinPdf();

        if ($pdfPromise !== null && $pdfActe !== null && $pdfTracfin !== null){
            return $this->json([
                'code'=> 400,
                'formView' => 'impossible d\'ajouter un document à ce dossier.'
            ], 400);
        }
        if ($pdfPromise == null && $pdfActe == null && $pdfTracfin == null){
            $label = "Charger le PDF du compromis, le fichier ne doit pas dépasser 20Mo de taille";
        }elseif($pdfPromise !== null && $pdfActe == null && $pdfTracfin == null){
            $label = "Charger le PDF de l'attestation d'acte de vente, le fichier ne doit pas dépasser 20Mo de taille";
        }elseif($pdfPromise !== null && $pdfActe !== null && $pdfTracfin == null){
            $label = "Charger le PDF du tracfin, le fichier ne doit pas dépasser 20Mo de taille";
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_adddocuments', ['id' => $transaction->getId()]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formDocuments_add',
                ],
            ])
            ->add('document', FileType::class,[
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

        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];
            $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
            // Récupération des données sous forme de tableau associatif
            $document = $form->get('document')->getData();

            //dd($transaction->getStep());

            if ($transaction->getStep() == 2){    // On ajoute la promesse de vente
                $newFilename = $this->addFiles($document, 'cv-', $pathdir, $pdfPromise);
                if($access === 'edit'){
                    $transaction->setPromisePdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setPromisePdfFilename($newFilename);
                    $transaction->setIsValidPromisepdf(1);
                    $transaction->setPromiseValidBy($this->getUser());
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
                $this->step($transaction);
            } elseif($transaction->getStep() == 7){
                //dd($transaction->getStep());
                if(!$transaction->getActePdfFilename() && !$transaction->getTracfinPdfFilename()){
                    $newFilename = $this->addFiles($document, 'av-', $pathdir, $pdfPromise);
                    if($access === 'edit'){
                        $transaction->setActePdfFilename($newFilename);
                        $em->flush();
                    }elseif ($access == 'admin'){
                        $transaction->setActePdfFilename($newFilename);
                        $transaction->setIsValidActepdf(1);
                        $transaction->setActeValidBy($this->getUser());
                        $em->flush();
                    }
                }elseif($transaction->getActePdfFilename() && !$transaction->getTracfinPdfFilename()){
                    $newFilename = $this->addFiles($document, 'tf-', $pathdir, $pdfPromise);
                    if($access === 'edit'){
                        $transaction->setTracfinPdfFilename($newFilename);
                        $em->flush();
                    }elseif ($access == 'admin'){
                        $transaction->setTracfinPdfFilename($newFilename);
                        $transaction->setIsValidtracfinPdf(1);
                        $em->flush();
                    }
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
                $this->step($transaction);
            }

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le document à été déposé sur le serveur.",
            ],$this->returnView($transaction, $access, 'gestapp/transaction/show/_documents.html.twig')), 200);;
        }

        $view = $this->render('gestapp/transaction/show/_fileForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);

    }

    #[Route('/{id}/edit_documents/{typeFile}', name: 'op_gestapp_transaction_editdocuments', methods: ['GET', 'POST'])]
    public function editDocuments(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $em,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger,
        transactionService $transactionService,
        $typeFile
    ): Response
    {
        $access = $this->access($transaction);

        $pdfPromise = $transaction->getPromisePdfFilename();
        $validPromise = $transaction->isIsValidPromisepdf();
        $pdfActe = $transaction->getActePdfFilename();
        $validActe = $transaction->isIsValidActepdf();
        $pdfTracfin = $transaction->getTracfinPdfFilename();
        $validTracfin = $transaction->isIsValidtracfinPdf();


        if ($typeFile == 'Prom'){
            $label = "Charger le nouveau PDF actualisé du compromis, le fichier ne doit pas dépasser 20Mo de taille";
        }elseif($typeFile == 'Ac'){
            $label = "Charger le nouveau PDF actualisé de l'attestation d'acte de vente, le fichier ne doit pas dépasser 20Mo de taille";
            $stepDoc = 2;
        }elseif($typeFile == 'Tf'){
            $label = "Charger le nouveau PDF actualisé du tracfin, le fichier ne doit pas dépasser 20Mo de taille";
            $stepDoc = 3;
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_editdocuments', [
                    'id' => $transaction->getId(),
                    'typeFile' => $typeFile
                ]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formDocuments_edit',
                ],
            ])
            ->add('document', FileType::class,[
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

        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];
            $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
            // Récupération des données sous forme de tableau associatif
            $file = $form->get('document')->getData();

            if ($typeFile === "Prom"){    // On ajoute la promesse de vente
                $newFilename = $this->addFiles($file, 'cv-', $pathdir, $pdfPromise);
                if($access === 'edit'){
                    $transaction->setPromisePdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setPromisePdfFilename($newFilename);
                    $transaction->setIsValidPromisepdf(1);
                    $transaction->setPromiseValidBy($this->getUser());
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
            }elseif($typeFile === "Ac"){
                $newFilename = $this->addFiles($file, 'av-', $pathdir, $pdfPromise);
                if($access === 'edit'){
                    $transaction->setActePdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setActePdfFilename($newFilename);
                    $transaction->setIsValidActepdf(1);
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
            }elseif($typeFile === "Tf"){
                $newFilename = $this->addFiles($file, 'tf-', $pathdir, $pdfPromise);
                if($access === 'edit'){
                    $transaction->setTracfinPdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setTracfinPdfFilename($newFilename);
                    $transaction->setIsValidtracfinPdf(1);
                    $transaction->setTracfinValidBy($this->getUser());
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
            }

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le document à été déposé sur le serveur.",
            ],$this->returnView($transaction, $access, 'gestapp/transaction/show/_documents.html.twig')), 200);
        }

        $view = $this->render('gestapp/transaction/show/_fileForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);

    }

    #[Route('/{id}/validFiles/{file}', name: 'op_gestapp_transaction_validfile', methods: ['GET','POST'])]
    public function validFiles(Request $request, Transaction $transaction, transactionService $transactionService, PropertyRepository $propertyRepository, EntityManagerInterface $em, $file){
        $access = $this->access($transaction);

        $data = json_decode($request->getContent(), true);
        $isValid = $data['option'] ?? null;
        $typeDoc = explode('-', $file)[0];

        if($isValid == 'validFile'){

            if($typeDoc == 'cv') {
                $transaction->setIsValidPromisepdf(1);
                $transaction->setPromiseValidBy($this->getUser());
            }elseif($typeDoc == 'fh'){
                $transaction->setIsValidHonoraires(1);
                $transaction->setHonorairesValidBy($this->getUser());
            }elseif($typeDoc == 'av'){
                $transaction->setIsValidActepdf(1);
                $transaction->setActeValidBy($this->getUser());
            }elseif($typeDoc == 'tf'){
                $transaction->setIsValidtracfinPdf(1);
                $transaction->setTracfinValidBy($this->getUser());
            }elseif($typeDoc == 'fact'){
                $transaction->setIsValidInvoicepdf(1);
                $transaction->setInvoiceValidBy($this->getUser());
            }
            $project = $transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            return $this->json([
                'code'=> 200,
                'message' => "Un RDV à été ajouté.",
            ], 200);
        }

        if($typeDoc == 'cv') {
            $fileName = $transaction->getPromisePdfFilename();
        }elseif($typeDoc == 'fh'){
            $fileName = $transaction->getHonorairesPdfFilename();
        }elseif($typeDoc == 'av'){
            $fileName = $transaction->getActePdfFilename();
        }elseif($typeDoc == 'tf'){
            $fileName = $transaction->getTracfinPdfFilename();
        }elseif($typeDoc == 'fact'){
            $fileName = $transaction->getInvoicePdfFilename();
        }

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];
        $pathdir = "/properties/".$newref."/documents/".$fileName;

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'path' => $pathdir,
        ], 200);

    }

    #[Route('/{id}/addinvoices', name: 'op_gestapp_transaction_addinvoices', methods: ['GET', 'POST'])]
    public function addInvoices(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $em,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger,
        transactionService $transactionService
    ): Response
    {
        $access = $this->access($transaction);

        $pdfHonoraire = $transaction->getHonorairesPdfFilename();
        $validHonoraire = $transaction->isIsValidHonoraires();
        $pdfInvoice = $transaction->getInvoicePdfFilename();
        $validInvoice = $transaction->isIsValidInvoicePdf();

        if ($pdfHonoraire !== null && $pdfInvoice !== null){
            return $this->json([
                'code'=> 400,
                'formView' => 'impossible d\'ajouter une facture à ce dossier.'
            ], 400);
        }
        if (!$pdfHonoraire && !$pdfInvoice){
            $label = "Charger le PDF de vos honoraires, le fichier ne doit pas dépasser 20Mo de taille";
        }elseif($pdfHonoraire && !$pdfInvoice){
            $label = "Charger le PDF de la facture finale, le fichier ne doit pas dépasser 20Mo de taille";
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_addinvoices', ['id' => $transaction->getId()]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formInvoice_add',
                ],
            ])
            ->add('document', FileType::class,[
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

        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];
            $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
            // Récupération des données sous forme de tableau associatif
            $document = $form->get('document')->getData();

            if ($transaction->getStep() == 4){    // On ajoute la promesse de vente
                $newFilename = $this->addFiles($document, 'fh-', $pathdir, $pdfHonoraire);
                if($access === 'edit'){
                    $transaction->setHonorairesPdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setHonorairesPdfFilename($newFilename);
                    $transaction->setIsValidHonoraires(1);
                    $transaction->setHonorairesValidBy($this->getUser());
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
                $this->step($transaction);
            } elseif($transaction->getStep() == 9){
                $newFilename = $this->addFiles($document, 'fact-', $pathdir, $pdfInvoice);
                if($access === 'edit'){
                    $transaction->setInvoicePdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setInvoicePdfFilename($newFilename);
                    $transaction->setIsValidInvoicepdf(1);
                    $transaction->setInvoiceValidBy($this->getUser());
                    $em->flush();
                }

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
                $this->step($transaction);
            }

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le document à été déposé sur le serveur.",
            ],$this->returnView($transaction, $access, 'gestapp/transaction/show/_invoices.html.twig')), 200);
        }

        $view = $this->render('gestapp/transaction/show/_fileForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);

    }

    #[Route('/{id}/editinvoices/{typeFile}', name: 'op_gestapp_transaction_editinvoices', methods: ['GET', 'POST'])]
    public function editInvoices(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $em,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger,
        transactionService $transactionService,
        $typeFile
    ): Response
    {
        $access = $this->access($transaction);

        $pdfHonoraire = $transaction->getHonorairesPdfFilename();
        $validHonoraire = $transaction->isIsValidHonoraires();
        $pdfInvoice = $transaction->getInvoicePdfFilename();
        $validInvoice = $transaction->isIsValidInvoicePdf();

        if ($typeFile == 'Ho'){
            $label = "Charger le nouveau PDF actualisé de vos honoraires, le fichier ne doit pas dépasser 20Mo de taille";
        }elseif($typeFile == 'Fa'){
            $label = "Charger le nouveau PDF actualisé de la facture finale, le fichier ne doit pas dépasser 20Mo de taille";
        }

        $form = $this->createFormBuilder(null,
            [
                'action' => $this->generateUrl('op_gestapp_transaction_editinvoices', [
                    'id' => $transaction->getId(),
                    'typeFile' => $typeFile
                ]),
                'method' => 'POST',
                'attr'   => [
                    'id' => 'formInvoice_edit',
                ],
            ])
            ->add('document', FileType::class,[
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

        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];
            $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
            // Récupération des données sous forme de tableau associatif
            $document = $form->get('document')->getData();

            if ($typeFile == 'Ho'){    // On ajoute la promesse de vente
                $newFilename = $this->addFiles($document, 'fh-', $pathdir, $pdfHonoraire);
                if($access === 'edit'){
                    $transaction->setHonorairesPdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setHonorairesPdfFilename($newFilename);
                    $transaction->setIsValidHonoraires(1);
                    $transaction->setHonorairesValidBy($this->getUser());
                    $em->flush();
                }
                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
            } elseif($typeFile == 'Fa'){
                $newFilename = $this->addFiles($document, 'fact-', $pathdir, $pdfInvoice);
                if($access === 'edit'){
                    $transaction->setInvoicePdfFilename($newFilename);
                    $em->flush();
                }elseif ($access == 'admin'){
                    $transaction->setInvoicePdfFilename($newFilename);
                    $transaction->setIsValidInvoicepdf(1);
                    $transaction->setInvoiceValidBy($this->getUser());
                    $em->flush();
                }

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();
            }

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le document à été déposé sur le serveur.",
            ],$this->returnView($transaction, $access, 'gestapp/transaction/show/_invoices.html.twig')), 200);
        }

        $view = $this->render('gestapp/transaction/show/_fileForm.html.twig', [
            'form' => $form
        ]);

        return $this->json([
            'code'=> 200,
            'message' => "Un RDV à été ajouté.",
            'formView' => $view->getContent(),
        ], 200);

    }

    #[Route('/2/{id}/show', name: 'op_gestapp_transaction_show', methods: ['GET'])]
    public function show(Request $request, Transaction $transaction, PhotoRepository $photoRepository): Response
    {
        $access = $this->access($transaction);

        $property = $transaction->getProperty();
        $customers = $transaction->getCustomer();
        $photo = $photoRepository->firstphoto($property->getId());
        $this->step($transaction);

        return $this->render('gestapp/transaction/show.html.twig', [
            'access' => $access,
            'transaction' => $transaction,
            'property' => $property,
            'customers' => $customers,
            'photo' => $photo,
        ]);
    }


    #[Route('/2/{id}', name: 'op_gestapp_transaction_show2', methods: ['GET'])]
    public function show2(Request $request, Transaction $transaction, PhotoRepository $photoRepository): Response
    {
        $property = $transaction->getProperty();
        $customers = $transaction->getCustomer();
        $photo = $photoRepository->firstphoto($property->getId());

        return $this->render('gestapp/transaction/show2.html.twig', [
            'transaction' => $transaction,
            'property' => $property,
            'customers' => $customers,
            'photo' => $photo
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // ---------------------------------------------------------------------------
    // Block 2
    // ---------------------------------------------------------------------------
    #[Route('/{id}/AddCustomer', name: 'op_gestapp_transaction_addcustomer', methods: ['GET', 'POST'])]
    public function addCustomer(
        Transaction $transaction,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        Request $request,
        EntityManagerInterface $em
    )
    {
        $user = $this->getUser();
        $property = $transaction->getProperty();
        $customerChoice = $customerChoiceRepository->find(2);

        $customer = new Customer();
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_transaction_addcustomer', [
                'id' => $transaction->getId()
            ]),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($form->get('firstName')->getData(), 0,3 ).substr($form->get('lastName')->getData(), 0,3 );
            // Ajout de l'acquéreur
            $customer->setRefCustomer($refCustomer);
            $customer->setRefEmployed($user);
            $customer->setCustomerChoice($customerChoice);
            $customer->addTransaction($transaction);
            $em->persist($customer);
            $em->flush();

            // liste tous les clients attachés à leur propriété
            $customers = $customerRepository->listbytransaction($transaction);

            if(count($customers) == 1)
            {
                $transaction->setState('promise');
                $em->persist($transaction);
                $em->flush();
            }

            return $this->json([
                'code'=> 200,
                'message' => "Le futur acquéreur a été correctement ajouté.",
                'liste' => $this->renderView('gestapp/transaction/include/block/_buyers.html.twig', [
                    'buyers' => $customers,
                    'transaction' => $transaction
                ]),
                'rowTable' => $this->renderView('gestapp/transaction/include/block/_rowPromise.html.twig', [
                    'transaction' => $transaction
                ])
            ], 200);
        }

        return $this->render('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);

    }

    // Ajout ou modification de la date de signature de la promesse de vente
    #[Route('/{id}/addDatePromise/{roleEditor}', name: 'op_gestapp_transaction_adddatepromise', methods: ['GET', 'POST'])]
    public function addDatePromise(Transaction $transaction, $roleEditor, Request $request, EntityManagerInterface $em, transactionService $transactionService) : response
    {
        $form = $this->createForm(Transactionstep2Type::class, $transaction, [
            'attr' => ['id'=>'addDatePromiseForm'],
            'action' => $this->generateUrl('op_gestapp_transaction_adddatepromise', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('deposit');
            $em->persist($transaction);
            $em->flush();

            $project = $transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Date de promesse de vente enregistrée.',
                'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                    'transaction' => $transaction,
                ]),

            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_adddatepromise.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification du compromis de vente en Pdf par le collaborateur
    #[Route('/{id}/addPromisePdf/{roleEditor}', name: 'op_gestapp_transaction_addpromisepdf', methods: ['GET', 'POST'])]
    public function addPromisePdf(
        Transaction $transaction,
        $roleEditor,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger,
        PropertyRepository $propertyRepository,
        transactionService $transactionService
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf_admin', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }
        //dd($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];

            // Suppression du PDF si booléen sur "true"
            $isSupprPromisePdf = $form->get('isSupprPromisePdf')->getData();
            if($isSupprPromisePdf && $isSupprPromisePdf == true){
                // récupération du nom de l'image
                $PromisePdfName = $transaction->getPromisePdfFilename();
                $pathPromisePdf = $this->getParameter('property_doc_directory')."/".$newref."/documents/".$PromisePdfName;
                // On vérifie si l'image existe
                if(file_exists($pathPromisePdf)){
                    unlink($pathPromisePdf);
                }
                $transaction->setPromisePdfFilename(null);
                $transaction->setIsSupprPromisePdf(0);
            }

            $promisepdf = $form->get('promisePdfFilename')->getData();
            $PromisePdfName = $transaction->getPromisePdfFilename();
            if($promisepdf){
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
                $pathfile = $pathdir.$PromisePdfName;
                if($PromisePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($promisepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'cv-'.$safeFilename.'.'.$promisepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $promisepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $promisepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $transaction->setPromisePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                if($this->submit == true){
                    if($hasAccess == false) {
                        $this->emailService->SubmitPdfForTransacAtAdmin(
                            'contact@papsimmo.fr',
                            'SoftPAPs',
                            $this->application->getAdminEmail(),
                            '[PAPs immo] : Une promesse de vente attend votre approbation.',
                            $transaction
                        );
                    }
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                    ]),
                    'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }else if($promisepdf){
                if($PromisePdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }
            }
        }

        return $this->render('gestapp/transaction/include/block/_addpromisepdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    // Validation de la promesse de vente par un Administrateur
    #[Route('/{id}/validPromisePdf/{roleEditor}', name: 'op_gestapp_transaction_validpromisepdf', methods: ['GET', 'POST'])]
    public function validPromisePdf(
        Request $request,
        $roleEditor,
        Transaction $transaction,
        transactionService $transactionService,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    )
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setDateAtPromise(new \Datetime('now'));
        $transaction->setPromiseValidBy($username);
        $transaction->setIsValidPromisepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $entityManager->flush();

        $employedEmail = $transaction->getRefEmployed()->getEmail();
        $fullHttp = $request->getUri();
        $host = parse_url($fullHttp, PHP_URL_HOST);
        if($this->submit == true){
            $email = (new TemplatedEmail())
                ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'));
                if($host == '127.0.0.1'){
                    $email
                        ->to('xavier.burke@openpixl.fr');
                }else{
                    $email
                        ->to($employedEmail);
                }
            $email
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("[PAPs Immo] : Document ". $transaction->getPromisePdfFilename() ." vérifié.")
                ->htmlTemplate('admin/mail/messageTransactionVerif.html.twig')
                ->context([
                    'transaction' => $transaction,
                    'url' => $request->server->get('HTTP_HOST')
                ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }
        }


        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction,
            ]),
            'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),
            'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addPromisePdfAdmin/{roleEditor}', name: 'op_gestapp_transaction_addpromisepdf_admin', methods: ['POST', 'GET'])]
    public function addPromisePdfAdmin(
        Request $request,
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger)
    {
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep3Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep3'],
            'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf_admin', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promisepdf = $form->get('promisePdfFilename')->getData();
            if($promisepdf){

                // Suppression du PDF si Présent
                $PromisePdfName = $transaction->getPromisePdfFilename();
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
                $pathfile = $pathdir.$PromisePdfName;
                if($PromisePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($promisepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'cv-'.$safeFilename.'.'.$promisepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $promisepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $promisepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setPromisePdfFilename($newFilename);
                $transaction->setState('definitive_sale');
                $transaction->setIsValidPromisepdf(1);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $entityManager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_addpromisepdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification du compromis de vente en Pdf par le collaborateur
    #[Route('/{id}/addHonorairePdf/{roleEditor}', name: 'op_gestapp_transaction_addhonorairepdf', methods: ['GET', 'POST'])]
    public function addHonorairePdf(
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger,
        PropertyRepository $propertyRepository
    ) : response
    {

        $form = $this->createForm(TransactionHonorairesType::class, $transaction, [
            'attr' => ['id'=>'transactionhonoraires'],
            'action' => $this->generateUrl('op_gestapp_transaction_addhonorairepdf', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        //dd($ref);
        $newref = $ref[0].'-'.$ref[1];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $honorairespdf = $form->get('honorairesPdfFilename')->getData();
            if($honorairespdf){
                // Suppression du PDF si Présent
                $honorairesPdfName = $transaction->getHonorairesPdfFilename();
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
                $pathfile = $pathdir.$honorairesPdfName;
                if($honorairesPdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($honorairespdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'fh-'.$safeFilename.'.'.$honorairespdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $honorairespdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $honorairespdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                if($this->submit == true){
                    $this->emailService->SubmitPdfForTransacAtAdmin(
                        'contact@papsimmo.fr',
                        'SoftPAPs',
                        $this->application->getAdminEmail(),
                        '[PAPs immo] : Une facture d\'honoraire a été versée au dossier.',
                        $transaction->getId()
                    );
                }

                $transaction->setHonorairesPdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }
        }

        return $this->render('gestapp/transaction/include/block/_addhonorairespdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/addDateActe/{roleEditor}', name: 'op_gestapp_transaction_adddateacte', methods: ['GET', 'POST'])]
    public function addDateActe(Transaction $transaction, transactionService $transactionService, $roleEditor, Request $request, EntityManagerInterface $em, MailerInterface $mailer) : response
    {
        $form = $this->createForm(TransactionActedateType::class, $transaction, [
            'attr' => ['id'=>'addDateActeForm'],
            'action' => $this->generateUrl('op_gestapp_transaction_adddateacte', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('definitive_sale');
            $em->persist($transaction);
            $em->flush();

            $project = $transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            if($this->submit){
                $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
                if($hasAccess == false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'))
                        ->to($this->application->getAdminEmail())
                        //->cc('cc@example.com')
                        //->bcc('bcc@example.com')
                        //->replyTo('fabien@example.com')
                        //->priority(Email::PRIORITY_HIGH)
                        ->subject('[PAPs immo] : TRANSACTION | Acte de vente signé sur la transaction :  '.$transaction->getId())
                        ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                        ->context([
                            'transaction' => $transaction,
                            'url' => $request->server->get('HTTP_HOST')
                        ]);
                    try {
                        $mailer->send($email);
                        $this->notificationService->setDocumentTransaction('SoftPAPs', 'Transaction', $transaction);
                    } catch (TransportExceptionInterface $e) {
                        // some error prevented the email sending; display an
                        // error message or try to resend the message
                        dd($e);
                    }
                }
            }

            return $this->json([
                'code' => 200,
                'message' => 'Date de signature acte de vente enregistrée.',
                'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                    'transaction' => $transaction
                ]),

            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_adddateacte.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification de l'attestation de vente en Pdf par le collaborateur
    #[Route('/{id}/addActePdf/{roleEditor}', name: 'op_gestapp_transaction_addactepdf', methods: ['GET', 'POST'])]
    public function addActePdf(
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionActepdfType::class, $transaction, [
                'attr' => ['id'=>'transactionactepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addactepdf', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionActepdfType::class, $transaction, [
                'attr' => ['id'=>'transactionactepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addactepdf_admin', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            // récupération de la référence
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];

            // Suppression du PDF si booléen sur "true"
            $isSupprActePdf = $form->get('isSupprActePdf')->getData();
            if($isSupprActePdf && $isSupprActePdf == true){
                // récupération du nom de l'image
                $ActePdfName = $transaction->getActePdfFilename();
                $pathActePdf = $this->getParameter('property_doc_directory')."/".$newref."/documents/".$ActePdfName;
                // On vérifie si l'image existe
                if(file_exists($pathActePdf)){
                    unlink($pathActePdf);
                }
                $transaction->setactePdfFilename(null);
                $transaction->setIsSupprActePdf(0);
            }

            $actepdf = $form->get('actePdfFilename')->getData();
            $actePdfName = $transaction->getActePdfFilename();
            if($actepdf){
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
                $pathfile = $pathdir.$actePdfName;
                if($actePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($actepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'av-'.$safeFilename.'.'.$actepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $actepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $actepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setActePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                if($this->submit == true){
                    if($hasAccess == false) {
                        $email = (new TemplatedEmail())
                            ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'))
                            ->to($this->application->getAdminEmail())
                            //->cc('cc@example.com')
                            //->bcc('bcc@example.com')
                            //->replyTo('fabien@example.com')
                            //->priority(Email::PRIORITY_HIGH)
                            ->subject('[PAPs immo] : Un document de transaction attend votre approbation')
                            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                            ->context([
                                'transaction' => $transaction,
                                'url' => $request->server->get('HTTP_HOST')
                            ]);
                        try {
                            $mailer->send($email);
                        } catch (TransportExceptionInterface $e) {
                            // some error prevented the email sending; display an
                            // error message or try to resend the message
                            dd($e);
                        }
                    }
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowacte' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),

                ], 200);
            }else if($actepdf){
                if($actePdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }

            }
        }

        return $this->render('gestapp/transaction/include/block/_addactepdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validActePdf/{roleEditor}', name: 'op_gestapp_transaction_validactepdf', methods: ['GET', 'POST'])]
    public function validActePdf(
        Request $request,
        $roleEditor,
        Transaction $transaction,
        transactionService $transactionService,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setDateAtSale(new \Datetime('now'));
        $transaction->setActeValidBy($username);
        $transaction->setIsValidActepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $entityManager->flush();

        $employedEmail = $transaction->getRefEmployed()->getEmail();
        $fullHttp = $request->getUri();
        $host = parse_url($fullHttp, PHP_URL_HOST);
        if($this->submit == true){
            $email = (new TemplatedEmail())
                ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'));
            if($host == '127.0.0.1'){
                $email
                    ->to('xavier.burke@openpixl.fr');
            }else{
                $email
                    ->to($employedEmail);
            }
            $email
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("[PAPs Immo] : Document vérifié")
                ->htmlTemplate('admin/mail/messageTransactionVerif.html.twig')
                ->context([
                    'transaction' => $transaction,
                ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }
        }

        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),
            'row' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),

        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addActePdfAdmin/{roleEditor}', name: 'op_gestapp_transaction_addactepdf_admin', methods: ['POST'])]
    public function addActePdfAdmin(
        Request $request,
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionActepdfType::class, $transaction, [
            'attr' => ['id'=>'transactionactepdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addactepdf_admin', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];

            $actepdf = $form->get('actePdfFilename')->getData();
            if($actepdf){
                // Supression du PDF si Présent
                $actePdfName = $transaction->getActePdfFilename();
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";
                $pathfile = $pathdir.$actePdfName;
                if($actePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($actepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'av-'.$safeFilename.'.'.$actepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $actepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $actepdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setActePdfFilename($newFilename);
                $transaction->setState('definitive_sale');
                $transaction->setIsValidActepdf(1);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $entityManager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowacte' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_addactepdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor'=> $roleEditor,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification de l'attestation de vente en Pdf par le collaborateur
    #[Route('/{id}/addTracfinPdf/{roleEditor}', name: 'op_gestapp_transaction_addtracfinpdf', methods: ['GET', 'POST'])]
    public function addTracfinPdf(
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
                'attr' => ['id'=>'transactiontracfinpdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
                'attr' => ['id'=>'transactiontracfinpdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf_admin', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);
        //dd($form->isSubmitted(), $form->isValid());
        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de la référence du dossier pour construire le chemin vers le dossier Property
            $property = $propertyRepository->find($transaction->getProperty()->getId());
            $ref = explode("/", $property->getRef());
            $newref = $ref[0].'-'.$ref[1];
            //dd($newref);

            // Suppression du PDF si booléen sur "true"
            $isSupprTracfinPdf = $form->get('isSupprTracfinPdf')->getData();
            if($isSupprTracfinPdf && $isSupprTracfinPdf == true){
                // récupération du nom de l'image
                $tracfinPdfName = $transaction->getTracfinPdfFilename();
                $pathTracfinPdf = $this->getParameter('property_doc_directory').'/documents/'.$tracfinPdfName;
                // On vérifie si l'image existe
                if(file_exists($pathTracfinPdf)){
                    unlink($pathTracfinPdf);
                }
                $transaction->setTracfinPdfFilename(null);
                $transaction->setIsSupprTracfinPdf(0);
            }

            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();
            $tracfinPdfName = $transaction->getTracfinPdfFilename();

            //dd($tracfinpdf, $tracfinPdfName);
            if($tracfinpdf){
                $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
                $pathfile = $pathdir.$tracfinPdfName;
                // Suppression du document si déjà présent en BDD.
                if($tracfinPdfName){
                    // On vérifie si le pdf existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                // Normalisation du nom de fichier
                $originalFilename = pathinfo($tracfinpdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'tf-'.$safeFilename.'.'.$tracfinpdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $tracfinpdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $tracfinpdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setTracfinPdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                if($this->submit == 1){
                    if($hasAccess == false) {
                        $email = (new TemplatedEmail())
                            ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'))
                            ->to($this->application->getAdminEmail())
                            //->cc('cc@example.com')
                            //->bcc('bcc@example.com')
                            //->replyTo('fabien@example.com')
                            //->priority(Email::PRIORITY_HIGH)
                            ->subject('[PAPs immo] : Un document de transaction attend votre approbation')
                            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                            ->context([
                                'transaction' => $transaction,
                                'url' => $request->server->get('HTTP_HOST')
                            ]);
                        try {
                            $mailer->send($email);
                        } catch (TransportExceptionInterface $e) {
                            // some error prevented the email sending; display an
                            // error message or try to resend the message
                            dd($e);
                        }
                    }
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);

            }else if($tracfinpdf){
                if($tracfinPdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }

            }
        }

        return $this->render('gestapp/transaction/include/block/_addtracfinpdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validTracfinPdf/{roleEditor}', name: 'op_gestapp_transaction_validtracfinpdf', methods: ['GET', 'POST'])]
    public function validTracfinPdf(
        Request $request,
        $roleEditor,
        Transaction $transaction,
        transactionService $transactionService,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    )
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setTracfinValidBy($username);
        $transaction->setIsValidtracfinPdf(1);
        $transaction->setIsDocsFinished(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $entityManager->flush();

        $employedEmail = $transaction->getRefEmployed()->getEmail();
        $fullHttp = $request->getUri();
        $host = parse_url($fullHttp, PHP_URL_HOST);
        if($this->submit == true){
            $email = (new TemplatedEmail())
                ->from(new Address('contact@papsimmo.fr', 'SoftPAPs'));
            if($host == '127.0.0.1'){
                $email
                    ->to('xavier.burke@openpixl.fr');
            }else{
                $email
                    ->to($employedEmail);
            }
            $email
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("[PAPs Immo] : Document vérifié")
                ->htmlTemplate('admin/mail/messageTransactionVerif.html.twig')
                ->context([
                    'transaction' => $transaction,
                ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }
        }


        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),
            'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ])
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addTracfinPdfAdmin/{roleEditor}', name: 'op_gestapp_transaction_addtracfinpdf_admin', methods: ['POST'])]
    public function addTracfinPdfAdmin(
        Request $request,
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger)
    {
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
            'attr' => ['id'=>'transactiontracfinpdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf_admin', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();
            if($tracfinpdf){

                // Supression du PDF si Présent
                $tracfinPdfName = $transaction->getTracfinPdfFilename();
                $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
                $pathfile = $pathdir.$tracfinPdfName;
                if($tracfinPdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $originalFilename = pathinfo($tracfinpdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'tf-'.$safeFilename.'.'.$tracfinpdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $tracfinpdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $tracfinpdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setTracfinPdfFilename($newFilename);
                $transaction->setState('finished');
                $transaction->setIsValidtracfinPdf(1);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $entityManager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ])
                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_addtracfinpdf.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification de l'attestation de vente en Pdf par le collaborateur
    #[Route('/{id}/addinvoicePdf/{roleEditor}', name: 'op_gestapp_transaction_addinvoicepdf', methods: ['GET', 'POST'])]
    public function addInvoicePdf(
        Transaction $transaction,
        transactionService $transactionService,
        $roleEditor,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger,
        PropertyRepository $propertyRepository,
    ) : response
    {
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        $submit = 1;
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
                'attr' => ['id'=>'transactioninvoicepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
                'attr' => ['id'=>'transactioninvoicepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf_admin', [
                    'id' => $transaction->getId(),
                    'roleEditor' => $roleEditor
                ]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoicepdf = $form->get('invoicePdfFilename')->getData();
            if($invoicepdf){
                // Supression du PDF si Présent
                $invoicePdfName = $transaction->getInvoicePdfFilename();
                $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
                $pathfile = $pathdir.$invoicePdfName;
                if($invoicePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }
                $mandataireFn = $transaction->getRefEmployed()->getFirstName();
                $mandataireLn = $transaction->getRefEmployed()->getLastName();
                $mandataire = $mandataireFn."_".$mandataireLn;
                $originalFilename = pathinfo($invoicepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'fcol-'.$mandataire."-".$safeFilename.".".$invoicepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $invoicepdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        mkdir($pathdir."/", 0775, true);
                        $invoicepdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setInvoicePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                if($submit == 1){
                    if($hasAccess == false) {
                        $email = (new TemplatedEmail())
                            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                            ->to('contact@papsimmo.com')
                            //->cc('cc@example.com')
                            //->bcc('bcc@example.com')
                            //->replyTo('fabien@example.com')
                            //->priority(Email::PRIORITY_HIGH)
                            ->subject('[PAPs immo] : Une facture a été déposée - '.$transaction->getName().'.')
                            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                            ->context([
                                'transaction' => $transaction,
                                'url' => $request->server->get('HTTP_HOST')
                            ]);
                        try {
                            $mailer->send($email);
                        } catch (TransportExceptionInterface $e) {
                            // some error prevented the email sending; display an
                            // error message or try to resend the message
                            dd($e);
                        }
                    }
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'Votre facture est déposé sur le site.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }
        }

        return $this->render('gestapp/transaction/include/block/_addinvoicepdf.html.twig', [
            'transaction' => $transaction,
            'roleEditor' => $roleEditor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validInvoicePdf/{roleEditor}', name: 'op_gestapp_transaction_validinvoicepdf_control', methods: ['GET', 'POST'])]
    public function validInvoicePdf(Request $request,Transaction $transaction, transactionService $transactionService, $roleEditor, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $transaction->setIsValidInvoicePdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction
            ]),
            'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                'transaction' => $transaction,
                'roleEditor' => $roleEditor
            ]),
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addInvoicePdfAdmin/{roleEditor}', name: 'op_gestapp_transaction_addinvoicepdf_admin', methods: ['POST'])]
    public function addInvoicePdfAdmin(
        Request $request,
        PropertyRepository $propertyRepository,
        Transaction $transaction,
        transactionService $transactionService,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        MailerInterface $mailer,
        $roleEditor)
    {
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $submit = 1;

        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
            'attr' => ['id'=>'transactioninvoicepdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf_admin', [
                'id' => $transaction->getId(),
                'roleEditor' => $roleEditor
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoicepdf = $form->get('invoicePdfFilename')->getData();
            if($invoicepdf){
                // Supression du PDF si Présent
                $invoicePdfName = $transaction->getInvoicePdfFilename();
                $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
                $pathfile = $pathdir.$invoicePdfName;
                if($invoicePdfName){
                    // On vérifie si l'image existe
                    if(file_exists($pathfile)){
                        unlink($pathfile);
                    }
                }

                $mandataire = $transaction->getRefEmployed()->getSlug();
                $originalFilename = pathinfo($invoicepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'fcol-'.$mandataire."-".$safeFilename.".".$invoicepdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $invoicepdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        mkdir($pathdir."/", 0775, true);
                        $invoicepdf->move(
                            $this->getParameter('property_doc_directory').$newref."/documents/",
                            $newFilename
                        );
                    }

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setInvoicePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                $project = $transactionService->calculateProject($transaction);
                $transaction->setProject($project);
                $em->flush();

                if($submit == 1){
                    if($hasAccess == false) {
                        $email = (new TemplatedEmail())
                            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                            ->to('contact@papsimmo.com')
                            //->cc('cc@example.com')
                            //->bcc('bcc@example.com')
                            //->replyTo('fabien@example.com')
                            //->priority(Email::PRIORITY_HIGH)
                            ->subject('[PAPs immo] : Une facture a été déposée - '.$transaction->getName().'.')
                            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                            ->context([
                                'transaction' => $transaction,
                                'url' => $request->server->get('HTTP_HOST')
                            ]);
                        try {
                            $mailer->send($email);
                        } catch (TransportExceptionInterface $e) {
                            // some error prevented the email sending; display an
                            // error message or try to resend the message
                            dd($e);
                        }
                    }
                }

                return $this->json([
                    'code' => 200,
                    'message' => 'Votre facture est déposé sur le site.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                        'transaction' => $transaction,
                        'roleEditor' => $roleEditor
                    ]),
                ], 200);
            }
        }

        return $this->render('gestapp/transaction/include/block/_addinvoicepdf.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/errordocument/{name}', name: 'op_gestapp_transaction_errordocument', methods: ['GET','POST'])]
    public function errorPdf(
        Request $request,
        Transaction $transaction,
        transactionService $transactionService,
        PropertyRepository $propertyRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em,
        $name)
    {
        $submit=0;
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        //responsable du dossier
        $email_resp = $property->getRefEmployed()->getEmail();

        $typeDoc = explode('-', $name)[0];
        //dd($typeDoc);
        $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
        $pathfile = $pathdir.$name;

        //dd($pathfile);

        if(file_exists($pathfile)){
            unlink($pathfile);
        }
        //dd(file_exists($pathfile));
        //dd($typeDoc);
        if($typeDoc == 'cv') {
            $transaction->setPromisePdfFilename(null);
            $transaction->setIsSupprPromisePdf(0);
        }elseif($typeDoc == 'av'){
            $transaction->setActePdfFilename(null);
            $transaction->setIsSupprActePdf(0);
        }elseif($typeDoc == 'tf') {
            $transaction->setTracfinPdfFilename(null);
            $transaction->setIsSupprTracfinPdf(0);
        }
        $em->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $em->flush();

        if($submit == 1){
            $email = (new TemplatedEmail())
                ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                ->to($email_resp)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject("[PAPs Immo] : Erreur sur le document présenté")
                ->htmlTemplate('admin/mail/messageErrorDocument.html.twig')
                ->context([
                    'transaction' => $transaction,
                    'url' => $request->server->get('HTTP_HOST'),
                    'typedoc' => $typeDoc
                ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }
        }

        return $this->json([
            'code' => 200,
            'message' => 'Un email a été envoyé à votre collaborateur pour lui signifier une erreur dans le document transmis.',
            'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                'transaction' => $transaction,
            ]),
            'rowacte' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                'transaction' => $transaction
            ]),
            'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                'transaction' => $transaction
            ]),
            'rowhonoraires' =>$this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                'transaction' => $transaction
            ]),
        ], 200);

    }

    #[Route('/{id}/closedfolder', name: 'op_gestapp_transaction_closedfolder', methods: ['GET'])]
    public function closedFolder(Transaction $transaction, TransactionRepository $transactionRepository, PropertyRepository $propertyRepository, EntityManagerInterface $em)
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $idproperty = $transaction->getProperty()->getId();
        $property = $propertyRepository->find($idproperty);
        $transaction->setIsClosedfolder(1);
        $property->setClosedFolder(1);

        $em->flush();

        if($hasAccess == true){
            $transactions = $transactionRepository->findBy(['isClosedfolder' => 0]);
        }else{
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId(), 'isClosedfolder' => 0]);
            dd($transaction);
        }

        return $this->json([
            'message' => 'Le dossier de vente à été fermé.',
            'liste' => $this->renderView('gestapp/transaction/include/_liste.html.twig', [
                'transactions' => $transactions
            ])
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }
        return $this->redirectToRoute('op_gestapp_transaction_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/del/{id}', name: 'op_gestapp_transaction_del', methods: ['POST'])]
    public function del(Transaction $transaction, TransactionRepository $transactionRepository, PropertyRepository $propertyRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $propertyId = $transaction->getProperty();
        $property = $propertyRepository->find($propertyId->getId());

        $property->setIsTransaction(0);
        $em->persist($property);

        // Suprression des documents dans leur répertoire
        // récupération du nom de l'image
        $PromisePdfName = $transaction->getPromisePdfFilename();
        $pathPromisePdf = $this->getParameter('transaction_promise_directory').'/'.$PromisePdfName;
        $ActePdfName = $transaction->getActePdfFilename();
        $pathActePdf = $this->getParameter('transaction_acte_directory').'/'.$PromisePdfName;
        $TracfinPdfName = $transaction->getTracfinPdfFilename();
        $pathTracfinPdf = $this->getParameter('transaction_tracfin_directory').'/'.$PromisePdfName;
        // On vérifie si les fichiers existe
        if(file_exists($PromisePdfName)){
            unlink($pathPromisePdf);
        }
        if(file_exists($ActePdfName)){
            unlink($pathActePdf);
        }
        if(file_exists($TracfinPdfName)){
            unlink($pathTracfinPdf);
        }
        $em->remove($transaction);
        $em->flush();

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        if($hasAccess == true){
            $transactions = $transactionRepository->findAll();
        }else{
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
        }

        return $this->json([
            'code'=>200,
            'accessAdmin' => $hasAccess,
            'liste' => $this->renderView('gestapp/transaction/include/_liste.html.twig', [
                'transactions' => $transactions
            ]),
            'ownliste' => $this->renderView('gestapp/transaction/include/_ownliste.html.twig', [
                'transactions' => $transactions
            ])
        ], 200);
    }

    #[Route('/addcustomerjson/{id}', name: 'op_gestapp_transaction_addcustomerjson',  methods: ['GET', 'POST'])]
    public function addCustomerJson(
        Transaction $transaction,
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        EntityManagerInterface $em,
    )
    {
        $access = $this->access($transaction);

        $employed = $employedRepository->find($this->getUser());

        $customer = new Customer();
        $customer->setRefEmployed($employed);
        $customer->setCustomerChoice($customerChoiceRepository->find(1));
        $customer->setTypeClient('particulier');
        $customer->addTransaction($transaction);
        $em->persist($customer);
        $em->flush();

        $form = $this->createForm(CustomerType::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_transaction_editcustomerjson', [
                'id'=> $transaction->getId(),
                'buyer' => $customer->getId(),
            ]),
            'method'=>'POST',
            'attr' => [
                'id' => 'formCustomer_add'
            ]
        ]);
        $form->handleRequest($request);

        $customerChoice = $customerChoiceRepository->find(2);
        if ($form->isSubmitted() && $form->isValid()) {
            // Contruction de la référence pour chaque propriété
            $date = new \DateTime();
            $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($form->get('firstName')->getData(), 0,3 ).substr($form->get('lastName')->getData(), 0,3 );
            $customer->setRefCustomer($refCustomer);
            $customer->setRefEmployed($employed);
            $customer->setCustomerChoice($customerChoice);
            $customer->addTransaction($transaction);

            // Ajouter le code d'insertion du fichier PDF
            // partie ajout CI
            $ci = $form->get('cifilename')->getData();
            $ciFilename = $customer->getCifilename();
            if($ci) {
                if ($ciFilename) {
                    $pathheader = $this->getParameter('customer_ci_directory') . '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/' .$ciFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = 'ci-'.$customer->getLastName().'_'.$customer->getFirstName().'.'.$ci->guessExtension();
                try {
                    $ci->move(
                        $this->getParameter('customer_ci_directory'). '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setCifilename($newFilename);
            }

            // partie Ajout Kbis
            $kbis = $form->get('kbisfilename')->getData();
            $kbisFilename = $customer->getKbisfilename();
            if($kbis) {
                if ($kbisFilename) {
                    $pathheader = $this->getParameter('customer_kbis_directory') . '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/' .$kbisFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = 'kbis-'.$customer->getLastName().'_'.$customer->getFirstName().'.'.$kbis->guessExtension();
                try {
                    $kbis->move(
                        $this->getParameter('customer_ci_directory'). '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setKbisfilename($newFilename);
            }

            // Ajout en BDD du nouveau client
            $customerRepository->add($customer);

            $this->step($transaction);
            $project = $this->transactionService->calculateProject($transaction);
            $transaction->setProject($project);
            $em->flush();

            // liste tous les clients attachés à leur propriété
            $customers = $customerRepository->listbytransaction($transaction);

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
            ],$this->returnView($transaction, $access,'gestapp/transaction/show/buyers.html.twig' )), 200);
        }

        $view = $this->render('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'formulaire présenté',
            'formView' => $view->getContent(),
            'deleteUrl' => $this->generateUrl('op_gestapp_transaction_delcustomerjson', [
                'id' => $transaction->getId(),
                'idCustomer' => $customer->getId()
            ])
        ]);
    }

    #[Route('/editcustomerjson/{id}/{buyer}', name: 'op_gestapp_transaction_editcustomerjson',  methods: ['GET', 'POST'])]
    public function editCustomerJson(
        Transaction $transaction,
        Request $request,
        $buyer,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
    )
    {
        $access = $this->access($transaction);

        $customer = $customerRepository->find($buyer);

        $idproperty = $transaction->getProperty()->getId();
        $form = $this->createForm(CustomerType::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_transaction_editcustomerjson', [
                'id'=> $transaction->getId(),
                'buyer' => $buyer,
            ]),
            'method'=>'POST',
            'attr' => [
                'id' => 'formCustomer_edit'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ajouter le code d'insertion du fichier PDF
            // partie ajout CI
            $ci = $form->get('cifilename')->getData();
            $ciFilename = $customer->getCifilename();
            if($ci) {
                if ($ciFilename) {
                    $pathheader = $this->getParameter('customer_ci_directory') . '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/' .$ciFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = 'ci-'.$customer->getLastName().'_'.$customer->getFirstName().'.'.$ci->guessExtension();
                try {
                    $ci->move(
                        $this->getParameter('customer_ci_directory'). '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setCifilename($newFilename);
            }

            // partie Ajout Kbis
            $kbis = $form->get('kbisfilename')->getData();
            $kbisFilename = $customer->getKbisfilename();
            if($kbis) {
                if ($kbisFilename) {
                    $pathheader = $this->getParameter('customer_kbis_directory') . '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/' .$kbisFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = 'kbis-'.$customer->getLastName().'_'.$customer->getFirstName().'.'.$kbis->guessExtension();
                try {
                    $kbis->move(
                        $this->getParameter('customer_ci_directory'). '/' .$customer->getLastName().'_'.$customer->getFirstName(). '/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setKbisfilename($newFilename);
            }

            $customer->setFinished(1);
            $customerRepository->add($customer);

            return $this->json(array_merge([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
            ],$this->returnView($transaction, $access,'gestapp/transaction/show/buyers.html.twig' )), 200);
        }

        // Affichage du formulaire de modification du client
        $view = $this->render('gestapp/customer/_form2.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'Modifier les informations du Client',
            'formView' => $view->getContent(),

        ],200);
    }

    #[Route('/delcustomerjson/{id}/{idCustomer}', name: 'op_gestapp_transaction_delcustomerjson',  methods: ['GET', 'POST'])]
    public function delCustomer(Transaction $transaction, $idCustomer, CustomerRepository $customerRepository, EntityManagerInterface $em)
    {
        $access = $this->access($transaction);

        $customer = $customerRepository->find($idCustomer);
        $transaction->removeCustomer($customer);
        $em->flush();

        $this->step($transaction);

        if($customer->isFinished() == 0){
            $em->remove($customer);
            $em->flush();
        }

        return $this->json(array_merge([
            'code' => 200,
            'message' => "L'acheteur a été retiré de la vente.",
            'view' => $this->renderView('gestapp/transaction/show/buyers.html.twig', [
                'transaction' => $transaction,
                'access' => $access
            ]),
        ], $this->returnView($transaction, $access, 'gestapp/transaction/show/buyers.html.twig' )), 200);
    }

    #[Route('/delappointment/{id}/{appointment}', name: 'op_gestapp_transaction_delappointment',  methods: ['GET', 'POST'])]
    public function delAppointment(Transaction $transaction, $appointment, EntityManagerInterface $em)
    {
        $access = $this->access($transaction);

        if ($appointment === 'dateAtPromise'){
            $transaction->setDateAtPromise(null);
        }
        else{
            $transaction->setDateAtSale(null);
        }
        $em->flush();

        $project = $this->transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $em->flush();

        $this->step($transaction);

        return $this->json(array_merge([
            'code' => 200,
            'message' => "L'acheteur a été retiré de la vente.",
        ], $this->returnView($transaction, $access, 'gestapp/transaction/show/_appointment.html.twig')), 200);
    }

    #[Route('/delfile/{id}/{document}', name: 'op_gestapp_transaction_delfile',  methods: ['GET','POST'])]
    public function delFile(
        Transaction $transaction,
        transactionService $transactionService,
        EntityManagerInterface $em,
        $document,
        PropertyRepository $propertyRepository
    )
    {
        $access = $this->access($transaction);
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());

        $newref = $ref[0].'-'.$ref[1];
        if($document == "Ac"){
            $name = $transaction->getActePdfFilename();
            $view = 'gestapp/transaction/show/_documents.html.twig';
        }elseif($document == "Ho"){
            $name = $transaction->getHonorairesPdfFilename();
            $view = 'gestapp/transaction/show/_invoices.html.twig';
        }elseif($document == "Fa"){
            $name = $transaction->getInvoicePdfFilename();
            $view = 'gestapp/transaction/show/_invoices.html.twig';
        }elseif($document == "Prom"){
            $name = $transaction->getPromisePdfFilename();
            $view = 'gestapp/transaction/show/_documents.html.twig';
        }elseif ($document == "Tf"){
            $name = $transaction->getTracfinPdfFilename();
            $view = 'gestapp/transaction/show/_documents.html.twig';
        }

        $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
        $pathfile = $pathdir.$name;
        if($name && file_exists($pathfile)){
            unlink($pathfile);
        }

        // Suppression en BDD du nom de fichier
        $typeDoc = explode('-', $name)[0];
        if($typeDoc == 'cv') {
            $transaction->setPromisePdfFilename(null);
            $transaction->setIsValidPromisepdf(0);
            $transaction->setPromiseValidBy(null);
            $transaction->setIsSupprPromisePdf(0);
        }elseif($typeDoc == 'fh'){
            $transaction->setHonorairesPdfFilename(null);
            $transaction->setIsValidHonoraires(0);
            $transaction->setHonorairesValidBy(null);
            $transaction->setIsSupprHonorairesPdf(0);
        }elseif($typeDoc == 'av'){
            $transaction->setActePdfFilename(null);
            $transaction->setIsValidActepdf(0);
            $transaction->setActeValidBy(null);
            $transaction->setIsSupprActePdf(0);
        }elseif($typeDoc == 'tf'){
            $transaction->setTracfinPdfFilename(null);
            $transaction->setIsValidtracfinPdf(0);
            $transaction->setTracfinValidBy(null);
            $transaction->setIsSupprTracfinPdf(0);
        }elseif($typeDoc == 'fact'){
            $transaction->setInvoicePdfFilename(null);
            $transaction->setIsValidInvoicepdf(0);
            $transaction->setInvoiceValidBy(null);
            $transaction->setIsSupprInvoicePdf(0);
        }
        $em->flush();

        $project = $transactionService->calculateProject($transaction);
        $transaction->setProject($project);
        $em->flush();
        $this->step($transaction);

        return $this->json(array_merge([
            'code' => 200,
            'message' => 'Le fichier a été correctement supprimé.',
        ], $this->returnView($transaction, $access, $view)));
    }
}
