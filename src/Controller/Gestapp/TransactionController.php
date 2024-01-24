<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\Customer2Type;
use App\Form\Gestapp\TransactionType;
use App\Form\Gestapp\Transactionstep2Type;
use App\Form\Gestapp\Transactionstep3Type;
use App\Form\Gestapp\Transactionstep4Type;
use App\Form\Gestapp\Transactionstep5Type;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\CustomerChoiceRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/gestapp/transaction')]
class TransactionController extends AbstractController
{
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

        return $this->renderForm('gestapp/transaction/new.html.twig', [
            'property' => $property,
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }



    #[Route('/add/{idproperty}', name: 'op_gestapp_transaction_add', methods: ['GET'])]
    public function add(Request $request, $idproperty, EntityManagerInterface $entityManager, PropertyRepository $propertyRepository)
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
        $transaction->setState('open');
        $transaction->setName($name);
        $transaction->setRefEmployed($user);
        $entityManager->persist($transaction);
        $property->setIsTransaction(1);
        $entityManager->persist($property);
        $entityManager->flush();

        return $this->redirectToRoute('op_gestapp_transaction_show', [
            'id' => $transaction->getId()
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_transaction_show', methods: ['GET'])]
    public function show(Request $request, Transaction $transaction, PhotoRepository $photoRepository): Response
    {

        $property = $transaction->getProperty();
        $customers = $transaction->getCustomer();
        $photo = $photoRepository->firstphoto($property->getId());

        return $this->render('gestapp/transaction/show.html.twig', [
            'transaction' => $transaction,
            'property' => $property,
            'customers' => $customers,
            'photo' => $photo
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
    // Block 1
    // ---------------------------------------------------------------------------

    #[Route('/step1/{id}', name: 'op_gestapp_transaction_step1', methods: ['POST'])]
    function step1(Transaction $transaction, EntityManagerInterface $entityManager, CustomerRepository $customerRepository,Request $request)
    {
        $customers = $transaction->getCustomer();

        if(count($customers) > 0){
            $transaction->setState('promise');
            $entityManager->persist($transaction);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Etape validée',
                'state' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                    'transaction' => $transaction
                ]),
                'blocks' => $this->renderView('gestapp/transaction/include/_blocks.html.twig', [
                    'transaction' => $transaction,
                    'customers' => $customers
                ])
            ],200);
        }else{
            return $this->json([
                'code' => 300,
                'message' => 'Attention, pas de client enregistré pour cette transaction.',
            ],200);
        }
    }

    #[Route('/{id}/step2', name: 'op_gestapp_transaction_step2', methods: ['GET', 'POST'])]
    public function step2(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Transactionstep2Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep2'],
            'action' => $this->generateUrl('op_gestapp_transaction_step2', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('deposit');
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Date de promesse de vente enregistrée.',
                'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                    'transaction' => $transaction
                ])
            ], 200);
        }

        return $this->render('gestapp/transaction/_formstep2.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // Dépôt du fichier Pdf de la promesse de vente par le collaborateur et l'administrateur - Step 3.
    #[Route('/{id}/LoadPromise', name: 'op_gestapp_transaction_step3_loadpromise', methods: ['GET', 'POST'])]
    public function LoadPromise(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        MailerInterface $mailer
    ): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_step3_loadpromise', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_step3_validLoadPromise', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Suppression du PDF si booléen sur "true"
            $isSupprPromisePdf = $form->get('isSupprPromisePdf')->getData();
            if($isSupprPromisePdf && $isSupprPromisePdf == true){
                // récupération du nom de l'image
                $PromisePdfName = $transaction->getPromisePdfFilename();
                $pathPromisePdf = $this->getParameter('transaction_promise_directory').'/'.$PromisePdfName;
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
                if($PromisePdfName){
                    $pathheader = $this->getParameter('transaction_promise_directory').'/'.$PromisePdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($promisepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$promisepdf->guessExtension();
                try {
                    $promisepdf->move(
                        $this->getParameter('transaction_promise_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setPromisePdfFilename($newFilename);
                $entityManager->persist($transaction);
                $entityManager->flush();

                if($hasAccess == false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                        ->to('xavier.burke@openpixl.fr')
                        //->cc('cc@example.com')
                        //->bcc('bcc@example.com')
                        //->replyTo('fabien@example.com')
                        //->priority(Email::PRIORITY_HIGH)
                        ->subject('[PAPs immo] : Un document de transaction attend votre approbation')
                        ->htmlTemplate('admin/mail/messageTransaction.html.twig')
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
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'step' => $this->renderView('gestapp/transaction/include/_step3.html.twig', [
                        'transaction' => $transaction
                    ])

                ], 200);
            }else if($promisepdf){
                if($PromisePdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }

            }
        }

        return $this->render('gestapp/transaction/_formstep3.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // Validation du fichier Pdf de la promesse par un administrateur - Step 3.
    #[Route('/{id}/validPromisebyAdmin', name: 'op_gestapp_transaction_step3_validPromisebyAdmin', methods: ['GET', 'POST'])]
    public function validPromisebyAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setPromiseValidBy($username);
        $transaction->setIsValidPromisepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to('xavier.burke@openpixl.fr')
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

        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction
            ]),

        ], 200);
    }

    // Validation du dépôt par un administrateur du fichier Pdf de la promesse de vente - Step 3.
    #[Route('/{id}/validLoadPromise', name: 'op_gestapp_transaction_step3_validLoadPromise', methods: ['POST'])]
    public function validLoadPromise(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep3Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep3'],
            'action' => $this->generateUrl('op_gestapp_transaction_step3_validLoadPromise', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $promisepdf = $form->get('promisePdfFilename')->getData();
            if($promisepdf){
                // Supression du PDF si Présent
                $PromisePdfName = $transaction->getPromisePdfFilename();
                if($PromisePdfName){
                    $pathheader = $this->getParameter('transaction_promise_directory').'/'.$PromisePdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($promisepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$promisepdf->guessExtension();
                try {
                    $promisepdf->move(
                        $this->getParameter('transaction_promise_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setPromisePdfFilename($newFilename);
                $transaction->setState('definitive_sale');
                $transaction->setIsValidPromisepdf(1);
                $entityManager->persist($transaction);
                $entityManager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'step' => $this->renderView('gestapp/transaction/include/_step3.html.twig', [
                        'transaction' => $transaction
                    ])

                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/_formstep3.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/loadActeOrTracfin', name: 'op_gestapp_transaction_step4_loadacteortracfin', methods: ['GET', 'POST'])]
    public function loadActeOrTracfin(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        MailerInterface $mailer
    ): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        if($hasAccess == false) {
            $form = $this->createForm(Transactionstep4Type::class, $transaction, [
                'attr' => ['id' => 'transactionstep4'],
                'action' => $this->generateUrl('op_gestapp_transaction_step4_loadacteortracfin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep4Type::class, $transaction, [
                'attr' => ['id' => 'transactionstep4'],
                'action' => $this->generateUrl('op_gestapp_transaction_step4_validloadacteortracfin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $actepdf = $form->get('actePdfFilename')->getData();
            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();
            //dd($tracfinpdf);
            if($actepdf || $tracfinpdf){
                if($actepdf){
                    // Supression du PDF si Présent
                    $actePdfName = $transaction->getActePdfFilename();
                    if($actePdfName){
                        $pathheader = $this->getParameter('transaction_acte_directory').'/'.$actePdfName;
                        // On vérifie si l'image existe
                        if(file_exists($pathheader)){
                            unlink($pathheader);
                        }
                    }
                    $originalFilename = pathinfo($actepdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'.'.$actepdf->guessExtension();
                    try {
                        $actepdf->move(
                            $this->getParameter('transaction_acte_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $transaction->setActePdfFilename($newFilename);
                    $entityManager->persist($transaction);
                    $entityManager->flush();

                    if($hasAccess == false) {
                        $email = (new TemplatedEmail())
                            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                            ->to('xavier.burke@openpixl.fr')
                            //->cc('cc@example.com')
                            //->bcc('bcc@example.com')
                            //->replyTo('fabien@example.com')
                            //->priority(Email::PRIORITY_HIGH)
                            ->subject('[PAPs Immo] : Un document de transaction attend votre approbation')
                            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
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

                }

                if($tracfinpdf){
                    // Supression du PDF si Présent
                    $tracfinPdfName = $transaction->getActePdfFilename();
                    if($tracfinPdfName){
                        $pathheader = $this->getParameter('transaction_tracfin_directory').'/'.$tracfinPdfName;
                        // On vérifie si l'image existe
                        if(file_exists($pathheader)){
                            unlink($pathheader);
                        }
                    }
                    $originalFilename = pathinfo($tracfinpdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'.'.$tracfinpdf->guessExtension();
                    try {
                        $tracfinpdf->move(
                            $this->getParameter('transaction_tracfin_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $transaction->setTracfinPdfFilename($newFilename);
                    $entityManager->persist($transaction);
                    $entityManager->flush();
                }

                return $this->json([
                    'code' => 200,
                    'message' => "L'attestation d'acte de vente PDF est déposé sur la plateforme en attente de validation.",
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'step' => $this->renderView('gestapp/transaction/include/_step4.html.twig', [
                        'transaction' => $transaction
                    ])

                ], 200);
            }


            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/_formstep4.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validActeOrTracfinbyAdmin', name: 'op_gestapp_transaction_step4_validacteortracfinbyadmin', methods: ['GET', 'POST'])]
    public function validActeOrTracfinbyAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Validation par l'admin de l'attestation de vente
        if($transaction->isIsValidActepdf() == 0 && $transaction->isIsValidtracfinPdf() == 0)
        {
            $user = $this->getUser();
            $username = $user->getFirstName()." ".$user->getLastName();
            $transaction->setIsValidActepdf(1);
            $transaction->setActeValidBy($username);
            $entityManager->persist($transaction);
            $entityManager->flush();

        }elseif($transaction->isIsValidActepdf() == 1 && $transaction->isIsValidtracfinPdf() == 0)
        {
            $user = $this->getUser();
            $username = $user->getFirstName()." ".$user->getLastName();
            $transaction->setIsValidActepdf(1);
            $transaction->setTracfinValidBy($username);
            $transaction->setState("finished");
            $entityManager->persist($transaction);
            $entityManager->flush();
        }

        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to('xavier.burke@openpixl.fr')
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

        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider le dossier de votre collaborateur. Un mail lui a été adressé afin de qu'il puisse continuer la vente",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction
            ]),
            'step' => $this->renderView('gestapp/transaction/include/_step4.html.twig', [
                'transaction' => $transaction
            ])
        ], 200);
    }

    #[Route('/{id}/validLoadActeorTracfin', name: 'op_gestapp_transaction_step4_validloadacteortracfin', methods: ['POST'])]
    public function validLoadActeorTracfin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep4Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep4'],
            'action' => $this->generateUrl('op_gestapp_transaction_step4_validloadacteortracfin', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $actepdf = $form->get('actePdfFilename')->getData();
            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();

            if($actepdf || $tracfinpdf){
                if($actepdf){
                    // Supression du PDF si Présent
                    $actePdfName = $transaction->getActePdfFilename();
                    if($actePdfName){
                        $pathheader = $this->getParameter('transaction_acte_directory').'/'.$actePdfName;
                        // On vérifie si l'image existe
                        if(file_exists($pathheader)){
                            unlink($pathheader);
                        }
                    }
                    $originalFilename = pathinfo($actepdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'.'.$actepdf->guessExtension();
                    try {
                        $actepdf->move(
                            $this->getParameter('transaction_acte_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $transaction->setActePdfFilename($newFilename);
                    $transaction->setState('definitive_sale');
                    $transaction->setIsValidActepdf(1);
                    $entityManager->persist($transaction);
                    $entityManager->flush();
                }

                if($tracfinpdf){
                    // Supression du PDF si Présent
                    $tracfinPdfName = $transaction->getActePdfFilename();
                    if($tracfinPdfName){
                        $pathheader = $this->getParameter('transaction_tracfin_directory').'/'.$tracfinPdfName;
                        // On vérifie si l'image existe
                        if(file_exists($pathheader)){
                            unlink($pathheader);
                        }
                    }
                    $originalFilename = pathinfo($tracfinpdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'.'.$tracfinpdf->guessExtension();
                    try {
                        $tracfinpdf->move(
                            $this->getParameter('transaction_tracfin_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $transaction->setTracfinPdfFilename($newFilename);
                    $transaction->setState('finished');
                    $transaction->setIsValidtracfinPdf(1);
                    $entityManager->persist($transaction);
                    $entityManager->flush();
                }
                return $this->json([
                    'code' => 200,
                    'message' => "L'attestation d'acte de vente PDF est déposé sur la plateforme en attente de validation.",
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'step' => $this->renderView('gestapp/transaction/include/_step4.html.twig', [
                        'transaction' => $transaction
                    ])

                ], 200);
            }

            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 300,
                'message' => "Il manque l'attestation d'acte de vente en pdf."
            ], 200);
        }

        return $this->render('gestapp/transaction/_formstep4.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/step5', name: 'op_gestapp_transaction_step5', methods: ['GET', 'POST'])]
    public function step5(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Transactionstep5Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep5'],
            'action' => $this->generateUrl('op_gestapp_transaction_step5', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        //dd($transaction);


        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('finished');
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Promesse de vente réalisée.'
            ], 200);
        }

        return $this->renderForm('gestapp/transaction/_formstep5.html.twig', [
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

            return $this->json([
                'code'=> 200,
                'message' => "Le futur acquéreur a été correctement ajouté.",
                'liste' => $this->renderView('gestapp/transaction/include/_buyers.html.twig', [
                    'buyers' => $customers,
                ])
            ], 200);
        }

        return $this->render('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);

    }

    #[Route('/{id}/DatePromise', name: 'op_gestapp_transaction_datepromise', methods: ['GET', 'POST'])]
    public function datePromise()
    {

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

        //dd($property);
        $em->remove($transaction);
        $em->flush();

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        //dd($user);

        if($hasAccess == true){
            $transactions = $transactionRepository->findAll();
        }else{
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
        }

        return $this->json([
            'code'=>200,
            'liste' => $this->renderView('gestapp/transaction/include/_liste.html.twig', [
                'transactions' => $transactions
            ])
        ], 200);
    }

}
