<?php

namespace App\Controller\Gestapp\Transaction;

use App\Entity\Gestapp\Transaction\AddCollTransac;
use App\Form\Gestapp\Transaction\AddCollTransacType;
use App\Repository\Gestapp\Transaction\AddCollTransacRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/transaction/add/coll/transac')]
class AddCollTransacController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_transaction_add_coll_transac_index', methods: ['GET'])]
    public function index(AddCollTransacRepository $addCollTransacRepository): Response
    {
        return $this->render('gestapp/transaction/add_coll_transac/index.html.twig', [
            'add_coll_transacs' => $addCollTransacRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_transaction_add_coll_transac_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $addCollTransac = new AddCollTransac();
        $form = $this->createForm(AddCollTransacType::class, $addCollTransac);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($addCollTransac);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_transaction_add_coll_transac_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/transaction/add_coll_transac/new.html.twig', [
            'add_coll_transac' => $addCollTransac,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_transaction_add_coll_transac_show', methods: ['GET'])]
    public function show(AddCollTransac $addCollTransac): Response
    {
        return $this->render('gestapp/transaction/add_coll_transac/show.html.twig', [
            'add_coll_transac' => $addCollTransac,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_transaction_add_coll_transac_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AddCollTransac $addCollTransac, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddCollTransacType::class, $addCollTransac);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestapp_transaction_add_coll_transac_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/transaction/add_coll_transac/edit.html.twig', [
            'add_coll_transac' => $addCollTransac,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_transaction_add_coll_transac_delete', methods: ['POST'])]
    public function delete(Request $request, AddCollTransac $addCollTransac, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$addCollTransac->getId(), $request->request->get('_token'))) {
            $entityManager->remove($addCollTransac);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestapp_transaction_add_coll_transac_index', [], Response::HTTP_SEE_OTHER);
    }
}
