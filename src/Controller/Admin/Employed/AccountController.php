<?php

namespace App\Controller\Admin\Employed;

use App\Entity\Admin\Employed\Account;
use App\Form\Admin\Employed\AccountType;
use App\Repository\Admin\Employed\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/employed/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'op_admin_employed_account_index', methods: ['GET'])]
    public function index(AccountRepository $accountRepository): Response
    {
        return $this->render('admin/employed/account/index.html.twig', [
            'accounts' => $accountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_admin_employed_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('op_admin_employed_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employed/account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_employed_account_show', methods: ['GET'])]
    public function show(Account $account): Response
    {
        return $this->render('admin/employed/account/show.html.twig', [
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_employed_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('op_admin_employed_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employed/account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_employed_account_delete', methods: ['POST'])]
    public function delete(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($account);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_admin_employed_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
