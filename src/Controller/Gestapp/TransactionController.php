<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Transaction;
use App\Form\Gestapp\TransactionType;
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
    public function show(Request $request, Transaction $transaction): Response
    {

        $property = $transaction->getProperty();
        $customers = $transaction->getCustomer();

        return $this->render('gestapp/transaction/show.html.twig', [
            'transaction' => $transaction,
            'property' => $property,
            'customers' => $customers
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

    #[Route('/{id}', name: 'op_gestapp_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
