<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\Customer2Type;
use App\Form\Gestapp\TransactionActedateType;
use App\Form\Gestapp\TransactionActepdfType;
use App\Form\Gestapp\TransactionTracfinpdfType;
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
use Symfony\Component\Routing\Attribute\Route;
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

        return $this->redirectToRoute('op_gestapp_transaction_show2', [
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
    #[Route('/{id}/addDatePromise', name: 'op_gestapp_transaction_adddatepromise', methods: ['GET', 'POST'])]
    public function addDatePromise(Transaction $transaction, Request $request, EntityManagerInterface $em) : response
    {
        $form = $this->createForm(Transactionstep2Type::class, $transaction, [
            'attr' => ['id'=>'addDatePromiseForm'],
            'action' => $this->generateUrl('op_gestapp_transaction_adddatepromise', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('deposit');
            $em->persist($transaction);
            $em->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Date de promesse de vente enregistrée.',
                'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                    'transaction' => $transaction
                ]),

            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_adddatepromise.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // Dépôt ou modification du compromis de vente en Pdf par le collaborateur
    #[Route('/{id}/addPromisePdf', name: 'op_gestapp_transaction_addpromisepdf', methods: ['GET', 'POST'])]
    public function addPromisePdf(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger
        ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf_admin', ['id' => $transaction->getId()]),
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
                $em->persist($transaction);
                $em->flush();

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
            'form' => $form,
        ]);
    }

    // Validation de la promesse de vente par un Administrateur
    #[Route('/{id}/validPromisePdf', name: 'op_gestapp_transaction_validpromisepdf', methods: ['GET', 'POST'])]
    public function validPromisePdf(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
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

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addPromisePdfAdmin', name: 'op_gestapp_transaction_addpromisepdf_admin', methods: ['POST'])]
    public function addPromisePdfAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep3Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep3'],
            'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf_admin', ['id' => $transaction->getId()]),
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

                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
        }

        return $this->render('gestapp/transaction/include/block/_addpromisepdf.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/addDateActe', name: 'op_gestapp_transaction_adddateacte', methods: ['GET', 'POST'])]
    public function addDateActe(Transaction $transaction, Request $request, EntityManagerInterface $em) : response
    {
        $form = $this->createForm(TransactionActedateType::class, $transaction, [
            'attr' => ['id'=>'addDateActeForm'],
            'action' => $this->generateUrl('op_gestapp_transaction_adddateacte', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('definitive_sale');
            $em->persist($transaction);
            $em->flush();

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
            'form' => $form,
        ]);
    }

    // Dépôt ou modification de l'attestation de vente en Pdf par le collaborateur
    #[Route('/{id}/addActePdf', name: 'op_gestapp_transaction_addactepdf', methods: ['GET', 'POST'])]
    public function addActePdf(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionActepdfType::class, $transaction, [
                'attr' => ['id'=>'transactionactepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addactepdf', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionActepdfType::class, $transaction, [
                'attr' => ['id'=>'transactionactepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addactepdf_admin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Suppression du PDF si booléen sur "true"
            $isSupprActePdf = $form->get('isSupprActePdf')->getData();
            if($isSupprActePdf && $isSupprActePdf == true){
                // récupération du nom de l'image
                $PromisePdfName = $transaction->getPromisePdfFilename();
                $pathPromisePdf = $this->getParameter('transaction_acte_directory').'/'.$PromisePdfName;
                // On vérifie si l'image existe
                if(file_exists($pathPromisePdf)){
                    unlink($pathPromisePdf);
                }
                $transaction->setactePdfFilename(null);
                $transaction->setIsSupprPromisePdf(0);
            }

            $actepdf = $form->get('actePdfFilename')->getData();
            $actePdfName = $transaction->getPromisePdfFilename();
            if($actepdf){
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
                        $this->getParameter('transaction_promise_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setPromisePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

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
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validActePdf', name: 'op_gestapp_transaction_validactepdf', methods: ['GET', 'POST'])]
    public function validActePdf(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setActeValidBy($username);
        $transaction->setIsValidActepdf(1);
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

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addActePdfAdmin', name: 'op_gestapp_transaction_addactepdf_admin', methods: ['POST'])]
    public function addActePdfAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionActepdfType::class, $transaction, [
            'attr' => ['id'=>'transactionactepdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addactepdf_admin', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $actepdf = $form->get('actePdfFilename')->getData();
            if($actepdf){
                // Supression du PDF si Présent
                $actePdfName = $transaction->getPromisePdfFilename();
                if($actePdfName){
                    $pathheader = $this->getParameter('transaction_acte_directory').'/'.$actePdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($actepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$actepdf->guessExtension();
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
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
            'form' => $form,
        ]);
    }

    // Dépôt ou modification de l'attestation de vente en Pdf par le collaborateur
    #[Route('/{id}/addTracfinPdf', name: 'op_gestapp_transaction_addtracfinpdf', methods: ['GET', 'POST'])]
    public function addTracfinPdf(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
                'attr' => ['id'=>'transactiontracfinpdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
                'attr' => ['id'=>'transactiontracfinpdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf_admin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Suppression du PDF si booléen sur "true"
            $isSupprTracfinPdf = $form->get('isSupprTracfinPdf')->getData();
            if($isSupprTracfinPdf && $isSupprTracfinPdf == true){
                // récupération du nom de l'image
                $tracfinPdfName = $transaction->getPromisePdfFilename();
                $pathTracfinPdf = $this->getParameter('transaction_tracfin_directory').'/'.$tracfinPdfName;
                // On vérifie si l'image existe
                if(file_exists($pathTracfinPdf)){
                    unlink($pathTracfinPdf);
                }
                $transaction->setTracfinPdfFilename(null);
            }

            $actepdf = $form->get('actePdfFilename')->getData();
            $actePdfName = $transaction->getPromisePdfFilename();
            if($actepdf){
                if($actePdfName){
                    $pathheader = $this->getParameter('transaction_tracfin_directory').'/'.$actePdfName;
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
                        $this->getParameter('transaction_tracfin_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setTracfinPdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

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

                ], 200);
            }else if($actepdf){
                if($actePdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }

            }
        }

        return $this->render('gestapp/transaction/include/block/_addtracfinpdf.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validTracfinPdf', name: 'op_gestapp_transaction_validtracfinpdf_control', methods: ['GET', 'POST'])]
    public function validTracfinPdf(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $username = $user->getFirstName()." ".$user->getLastName();
        $transaction->setState('definitive_sale');
        $transaction->setTracfinValidBy($username);
        $transaction->setIsValidtracfinPdf(1);
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

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addTracfinPdfAdmin', name: 'op_gestapp_transaction_addtracfinpdf_admin', methods: ['POST'])]
    public function addTracfinPdfAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionTracfinpdfType::class, $transaction, [
            'attr' => ['id'=>'transactiontracfinpdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addactepdf_admin', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();
            if($tracfinpdf){
                // Supression du PDF si Présent
                $tracfinPdfName = $transaction->getTracfinPdfFilename();
                if($tracfinPdfName){
                    $pathheader = $this->getParameter('transaction_tracfin_directory').'/'.$tracfinPdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($tracfinpdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$tracfinpdf->guessExtension();
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),

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

    #[Route('/addcustomerjson/{type}/{option}', name: 'op_gestapp_transaction_addcustomerjson',  methods: ['GET', 'POST'])]
    public function addCustomerJson(
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        $type,
        $option
    )
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        $transac = $transactionRepository->find($option);
        $customer = new Customer();

        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_transaction_addcustomerjson', [
                'id'=> $customer->getId(),
                'type' => $type,
                'option' => $option
            ]),
            'method'=>'POST'
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
            $customer->addTransaction($transac);

            // Ajout en BDD du nouveau client
            $customerRepository->add($customer);

            // liste tous les clients attachés à leur propriété
            $customers = $customerRepository->listbytransaction($transac);

            return $this->json([
                'code'=> 200,
                'message' => "L'acheteur a été correctement ajouté.",
                'liste' => $this->renderView('gestapp/transaction/include/block/_customers.html.twig', [
                    'transaction' => $transac,
                    'type' => 2
                ]),
                'type' => 2
            ], 200);
        }


        //dd('erreur soumission');

        $view = $this->render('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'formulaire présenté',
            'formView' => $view->getContent()
        ]);
    }

    #[Route('/editcustomerjson/{id}/{type}/{option}', name: 'op_gestapp_transaction_editcustomerjson',  methods: ['GET', 'POST'])]
    public function editCustomerJson(
        Request $request,
        Customer $customer,
        $type,
        $option,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
    )
    {
        $transac = $transactionRepository->find($option);
        $idproperty = $transac->getProperty()->getId();
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_transaction_editcustomerjson', [
                'id'=> $customer->getId(),
                'type' => $type,
                'option' => $option
            ]),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);

        if($type == 1) {
            $property = $propertyRepository->find($option);
            if ($form->isSubmitted() && $form->isValid()) {
                $customerRepository->add($customer);
                return $this->json([
                    'code'=> 200,
                    'type' => 1,
                    'message' => "Le vendeur a été correctement modifié.",
                    'liste' => $this->renderView('gestapp/transaction/include/block/_customers.html.twig', [
                        'transaction' => $transac,
                        'type' => $type
                    ])
                ], 200);
            }
            $customers = $customerRepository->listbyproperty($idproperty);
            // Affichage du formulaire de modification du client
            $view = $this->render('gestapp/customer/_form2.html.twig', [
                'customer' => $customer,
                'form' => $form
            ]);

            return $this->json([
                'code' => 200,
                'message' => 'Modifier les informations du Client',
                'formView' => $view->getContent()
            ],200);
        }else{
            if ($form->isSubmitted() && $form->isValid()) {
                $customerRepository->add($customer);
                return $this->json([
                    'code'=> 200,
                    'type' => 2,
                    'message' => "Le vendeur a été correctement modifié.",
                    'liste' => $this->renderView('gestapp/transaction/include/block/_customers.html.twig', [
                        'transaction' => $transac,
                        'type' => $type
                    ])
                ], 200);
            }
            // Affichage du formulaire de modification du client
            $view = $this->render('gestapp/customer/_form2.html.twig', [
                'customer' => $customer,
                'form' => $form
            ]);

            return $this->json([
                'code' => 200,
                'message' => 'Modifier les informations du Client',
                'formView' => $view->getContent()
            ],200);
        }
    }

    #[Route('/delcustomerjson/{id}/{idCustomer}', name: 'op_gestapp_transaction_delcustomerjson',  methods: ['GET', 'POST'])]

    public function delCustomer(Transaction $transaction, $idCustomer, CustomerRepository $customerRepository, EntityManagerInterface $em)
    {
        $customer = $customerRepository->find($idCustomer);
        $transaction->removeCustomer($customer);
        $em->flush();

        return $this->json([
            'code' => 200,
            'liste' => $this->renderView('gestapp/transaction/include/block/_customers.html.twig', [
                'transaction' => $transaction,
                'type' => 2
            ])
        ], 200);
    }

}
