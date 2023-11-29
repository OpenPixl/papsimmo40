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

#[Route('/gestapp/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('gestapp/transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(),
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

        return $this->renderForm('gestapp/transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
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

        //dd($transaction);


        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('quotation');
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Promesse de vente réalisée.'
            ], 200);
        }

        $view = $this->render('gestapp/transaction/_formstep2.html.twig', [
            'transaction' => $transaction,
            'form' => $form
        ]);

        return $this->renderForm('gestapp/transaction/_formstep2.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/step3', name: 'op_gestapp_transaction_step3', methods: ['GET', 'POST'])]
    public function step3(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Transactionstep3Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep3'],
            'action' => $this->generateUrl('op_gestapp_transaction_step3', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        //dd($transaction);


        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('definitive_sale');
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Promesse de vente réalisée.'
            ], 200);
        }

        return $this->renderForm('gestapp/transaction/_formstep3.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/step4', name: 'op_gestapp_transaction_step4', methods: ['GET', 'POST'])]
    public function step4(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Transactionstep4Type::class, $transaction, [
            'attr' => ['id'=>'transactionstep4'],
            'action' => $this->generateUrl('op_gestapp_transaction_step4', ['id' => $transaction->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        //dd($transaction);


        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setState('key_delivery');
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Promesse de vente réalisée.'
            ], 200);
        }

        return $this->renderForm('gestapp/transaction/_formstep4.html.twig', [
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

    #[Route('/secondstep/{id}', name: 'op_gestapp_transaction_secondstep', methods: ['POST'])]
    function secondstep(Transaction $transaction, EntityManagerInterface $entityManager, CustomerRepository $customerRepository,Request $request)
    {
        $listCustomer = $customerRepository->findCustomerWithTransaction($transaction->getId());
        //dd($listCustomer);
        if($listCustomer){
            $transaction->setState('promise');
            $entityManager->persist($transaction);
            $entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Etape validée',
            ],200);
        }else{
            return $this->json([
                'code' => 300,
                'message' => 'Attention, pas de client enregistré pour cette transaction.',
            ],200);
        }



    }
}
