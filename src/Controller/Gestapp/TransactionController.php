<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\TransactionType;
use App\Form\Gestapp\Transactionstep2Type;
use App\Form\Gestapp\Transactionstep3Type;
use App\Form\Gestapp\Transactionstep4Type;
use App\Form\Gestapp\Transactionstep5Type;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    #[Route('/{id}/step3', name: 'op_gestapp_transaction_step3', methods: ['GET', 'POST'])]
    public function step3(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        if($hasAccess == false){
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_step3', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep3Type::class, $transaction, [
                'attr' => ['id'=>'transactionstep3'],
                'action' => $this->generateUrl('op_gestapp_transaction_validAdminToStepFour', ['id' => $transaction->getId()]),
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

    #[Route('/{id}/validAdminStep3', name: 'op_gestapp_transaction_validAdminStep3', methods: ['POST'])]
    public function validAdminStep3(Request $request, Transaction $transaction, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $transaction->setState('definitive_sale');
        $transaction->setIsValidPromisepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->json([
            'code' => 300,
            'message' => "Vous venez de valider le dossier de votre collaborateur. Un mail lui a été adressé afin de qu'il puisse continuer la vente",
            'transState' => $this->renderView('gestapp/transaction/include/_barandstep.html.twig', [
                'transaction' => $transaction
            ]),

        ], 200);
    }
    #[Route('/{id}/validAdminToStepFour', name: 'op_gestapp_transaction_validAdminToStepFour', methods: ['POST'])]
    public function validAdminToStepFour(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep3Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep3'],
            'action' => $this->generateUrl('op_gestapp_transaction_step3', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $promisepdf = $form->get('promisePdfFilename')->getData();
            //dd($pdf);
            if($promisepdf){
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


    #[Route('/{id}/step4', name: 'op_gestapp_transaction_step4', methods: ['GET', 'POST'])]
    public function step4(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        if($hasAccess == false) {
            $form = $this->createForm(Transactionstep4Type::class, $transaction, [
                'attr' => ['id' => 'transactionstep4'],
                'action' => $this->generateUrl('op_gestapp_transaction_step4', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }else{
            $form = $this->createForm(Transactionstep4Type::class, $transaction, [
                'attr' => ['id' => 'transactionstep4'],
                'action' => $this->generateUrl('op_gestapp_transaction_validActeByAdmin', ['id' => $transaction->getId()]),
                'method' => 'POST'
            ]);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $actepdf = $form->get('actePdfFilename')->getData();
            if($actepdf){
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
                $entityManager->persist($transaction);
                $entityManager->flush();

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

            $tracfinpdf = $form->get('tracfinPdfFilename')->getData();
            if($tracfinpdf){
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
                $entityManager->persist($transaction);
                $entityManager->flush();

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

    #[Route('/{id}/validAdminActeStep4', name: 'op_gestapp_transaction_validAdminActeStep4', methods: ['GET', 'POST'])]
    public function validAdminActeStep4(Request $request, Transaction $transaction, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $transaction->setIsValidActepdf(1);
        $entityManager->persist($transaction);
        $entityManager->flush();

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

    #[Route('/{id}/validActeByAdmin', name: 'op_gestapp_transaction_validActeByAdmin', methods: ['POST'])]
    public function validActeByAdmin(Request $request, Transaction $transaction, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(Transactionstep4Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep4'],
            'action' => $this->generateUrl('op_gestapp_transaction_validActeByAdmin', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($transaction);
            $actepdf = $form->get('actePdfFilename')->getData();
            //dd($pdf);
            if($actepdf){
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
                    'message' => "Attestation d'acte de vente déposée.",
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
        //dd($property);
        $em->remove($transaction);
        $em->flush();

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        if($hasAccess == true){
            $transactions = $transactionRepository->findAll();
        }else{
            $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
        }

        return $this->json([
            'code'=>200,
            'liste' => $this->renderView('_ownliste.html.twig', [
                'transactions' => $transactions
            ])
        ], 200);
    }

}
