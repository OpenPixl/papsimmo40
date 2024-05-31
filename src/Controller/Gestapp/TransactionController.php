<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\Customer2Type;
use App\Form\Gestapp\TransactionActedateType;
use App\Form\Gestapp\TransactionActepdfType;
use App\Form\Gestapp\TransactionHonorairesType;
use App\Form\Gestapp\TransactionInvoicepdfType;
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
use function Symfony\Component\Clock\now;

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
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId(), 'isClosedfolder' => 0]);
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

        return $this->render('gestapp/transaction/new.html.twig', [
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
    public function addDatePromise(Transaction $transaction, $roleEditor, Request $request, EntityManagerInterface $em) : response
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
        PropertyRepository $propertyRepository
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
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
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
            'roleEditor' => $roleEditor,
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
        $transaction->setDateAtPromise(new \Datetime('now'));
        $transaction->setPromiseValidBy($username);
        $transaction->setIsValidPromisepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $employedEmail = $transaction->getRefEmployed()->getEmail();

        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to($employedEmail)
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
            'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                'transaction' => $transaction
            ]),
            'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                'transaction' => $transaction
            ]),
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addPromisePdfAdmin', name: 'op_gestapp_transaction_addpromisepdf_admin', methods: ['POST'])]
    public function addPromisePdfAdmin(
        Request $request,
        Transaction $transaction,
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
            'action' => $this->generateUrl('op_gestapp_transaction_addpromisepdf_admin', ['id' => $transaction->getId()]),
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowhonoraires' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
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

    // Dépôt ou modification du compromis de vente en Pdf par le collaborateur
    #[Route('/{id}/addHonorairePdf/{roleEditor}', name: 'op_gestapp_transaction_addhonorairepdf', methods: ['GET', 'POST'])]
    public function addHonorairePdf(
        Transaction $transaction,
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

                $transaction->setHonorairesPdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowhonorairespdf.html.twig', [
                        'transaction' => $transaction
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
    public function addDateActe(Transaction $transaction, $roleEditor, Request $request, EntityManagerInterface $em) : response
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

                if($hasAccess == false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                        ->to('contact@papsimmo.com')
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowacte' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
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
            'roleEditor' => $roleEditor,
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
        $transaction->setDateAtSale(new \Datetime('now'));
        $transaction->setActeValidBy($username);
        $transaction->setIsValidActepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $employedEmail = $transaction->getRefEmployed()->getEmail();

        $email = (new TemplatedEmail())
             ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to($employedEmail)
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
            'row' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                'transaction' => $transaction
            ]),

        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addActePdfAdmin', name: 'op_gestapp_transaction_addactepdf_admin', methods: ['POST'])]
    public function addActePdfAdmin(
        Request $request,
        Transaction $transaction,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        SluggerInterface $slugger)
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowacte' => $this->renderView('gestapp/transaction/include/block/_rowactepdf.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
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
    #[Route('/{id}/addTracfinPdf/{roleEditor}', name: 'op_gestapp_transaction_addtracfinpdf', methods: ['GET', 'POST'])]
    public function addTracfinPdf(
        Transaction $transaction,
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

                if($hasAccess == false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
                        ->to('contact@papsimmo.com')
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Le document PDF est déposé sur la plateforme en attente de validation.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction
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

    #[Route('/{id}/validTracfinPdf', name: 'op_gestapp_transaction_validtracfinpdf', methods: ['GET', 'POST'])]
    public function validTracfinPdf(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
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

        $employedEmail = $transaction->getRefEmployed()->getEmail();
        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to($employedEmail)
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
            'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                'transaction' => $transaction
            ])
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addTracfinPdfAdmin', name: 'op_gestapp_transaction_addtracfinpdf_admin', methods: ['POST'])]
    public function addTracfinPdfAdmin(
        Request $request,
        Transaction $transaction,
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
            'action' => $this->generateUrl('op_gestapp_transaction_addtracfinpdf_admin', ['id' => $transaction->getId()]),
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

                return $this->json([
                    'code' => 200,
                    'message' => 'Promesse de vente réalisée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'rowtracfin' => $this->renderView('gestapp/transaction/include/block/_rowtracfinpdf.html.twig', [
                        'transaction' => $transaction
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
    #[Route('/{id}/addinvoicePdf', name: 'op_gestapp_transaction_addinvoicepdf', methods: ['GET', 'POST'])]
    public function addInvoicePdf(
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SluggerInterface $slugger
    ) : response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        if($hasAccess == false){
            $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
                'attr' => ['id'=>'transactioninvoicepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
                'attr' => ['id'=>'transactioninvoicepdf'],
                'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf_admin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Suppression du PDF si booléen sur "true"
            $isSupprInvoicePdf = $form->get('isSupprInvoicePdf')->getData();
            if($isSupprInvoicePdf && $isSupprInvoicePdf == true){
                // récupération du nom de l'image
                $invoicePdfName = $transaction->getInvoicePdfFilename();
                $pathInvoicePdf = $this->getParameter('transaction_tracfin_directory').'/'.$invoicePdfName;
                // On vérifie si l'image existe
                if(file_exists($pathInvoicePdf)){
                    unlink($pathInvoicePdf);
                }
                $transaction->setInvoicePdfFilename(null);
            }

            $invoicepdf = $form->get('invoicePdfFilename')->getData();
            $invoicePdfName = $transaction->getinvoicePdfFilename();
            if($invoicepdf){
                if($invoicePdfName){
                    $pathheader = $this->getParameter('transaction_invoice_directory').'/'.$invoicePdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($invoicepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$invoicepdf->guessExtension();
                try {
                    $invoicepdf->move(
                        $this->getParameter('transaction_invoice_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setInvoicePdfFilename($newFilename);
                $em->persist($transaction);
                $em->flush();

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

                return $this->json([
                    'code' => 200,
                    'message' => 'Votre facture est déposé sur le site.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                        'transaction' => $transaction
                    ]),
                ], 200);

            }else if($invoicepdf){
                if($invoicePdfName){
                    dd('doc pdf présent');
                }else{
                    dd('pas de doc');
                }
            }
        }

        return $this->render('gestapp/transaction/include/block/_addinvoicepdf.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/validInvoicePdf', name: 'op_gestapp_transaction_validinvoicepdf_control', methods: ['GET', 'POST'])]
    public function validInvoicePdf(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $transaction->setIsValidInvoicePdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => "Vous venez de valider la promesse de vente de votre collaborateur. <br>
                          Un mail lui a été adressé afin de qu'il puisse continuer le processus de vente.",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction
            ]),
            'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                'transaction' => $transaction
            ]),
        ], 200);
    }

    // Dépôt ou modification du compromis de vente en Pdf par un administrateur
    #[Route('/{id}/addInvoicePdfAdmin', name: 'op_gestapp_transaction_addinvoicepdf_admin', methods: ['POST'])]
    public function addInvoicePdfAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(TransactionInvoicepdfType::class, $transaction, [
            'attr' => ['id'=>'transactioninvoicepdf'],
            'action' => $this->generateUrl('op_gestapp_transaction_addinvoicepdf_admin', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $invoicepdf = $form->get('invoicePdfFilename')->getData();
            if($invoicepdf){
                // Supression du PDF si Présent
                $invoicePdfName = $transaction->getTracfinPdfFilename();
                if($invoicePdfName){
                    $pathheader = $this->getParameter('transaction_invoice_directory').'/'.$invoicePdfName;
                    // On vérifie si l'image existe
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($invoicepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$invoicepdf->guessExtension();
                try {
                    $invoicepdf->move(
                        $this->getParameter('transaction_invoice_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $transaction->setInvoicePdfFilename($newFilename);
                $transaction->setIsValidinvoicePdf(1);
                $entityManager->persist($transaction);
                $entityManager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'La Facture a étét correctement déposée.',
                    'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                        'transaction' => $transaction
                    ]),
                    'row' => $this->renderView('gestapp/transaction/include/block/_rowinvoicepdf.html.twig', [
                        'transaction' => $transaction
                    ])
                ], 200);
            }

            return $this->json([
                'code' => 300,
                'message' => 'Il manque le document en pdf.'
            ], 200);
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
        PropertyRepository $propertyRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em,
        $name)
    {
        // action ne pouvant être réalisée uniquement par un admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

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

        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to('xavier.burke@openpixl.fr')
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

        return $this->json([
            'code' => 200,
            'message' => 'Un email a été envoyé à votre collaborateur pour lui signifier une erreur dans le document transmis.',
            'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                'transaction' => $transaction
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
            ])
        ], 200);
    }

    #[Route('/deldocument/{id}/{name}', name: 'op_gestapp_transaction_deldocument',  methods: ['GET','POST'])]
    public function delDocument(Transaction $transaction, EntityManagerInterface $em, $name, PropertyRepository $propertyRepository)
    {
        // récupération de la référence du dossier pour construire le chemin vers le dossier Property
        $property = $propertyRepository->find($transaction->getProperty()->getId());
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];
        $pathdir = $this->getParameter('property_doc_directory').$newref."/documents/";
        $pathfile = $pathdir.$name;
        if(file_exists($pathfile)){
            unlink($pathfile);
        }

        // Suppression en BDD du nom de fichier
        $typeDoc = explode('-', $name)[0];
        if($typeDoc == 'cv') {
            $transaction->setPromisePdfFilename(null);
            $transaction->setIsValidPromisepdf(0);
            $transaction->setDateAtPromise(null);
            $transaction->setIsSupprPromisePdf(0);
        }elseif($typeDoc == 'fh'){
            $transaction->setHonorairesPdfFilename(null);
            $transaction->setIsSupprHonorairesPdf(0);
        }elseif($typeDoc == 'av'){
            $transaction->setActePdfFilename(null);
            $transaction->setIsValidActepdf(0);
            $transaction->setIsSupprActePdf(0);
        }elseif($typeDoc == 'tf'){
            $transaction->setTracfinPdfFilename(null);
            $transaction->setIsValidtracfinPdf(0);
            $transaction->setIsSupprTracfinPdf(0);
        }
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Le fichier a été correctement supprimé.',
            'rowpromise' => $this->renderView('gestapp/transaction/include/block/_rowpromisepdf.html.twig', [
                'transaction' => $transaction
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
        ]);
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
