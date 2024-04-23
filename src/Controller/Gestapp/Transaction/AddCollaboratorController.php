<?php

namespace App\Controller\Gestapp\Transaction;

use App\Repository\Gestapp\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCollaboratorController extends AbstractController
{
    #[Route('/gestapp/transaction/add/collaborator', name: 'app_gestapp_transaction_add_collaborator')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $listCollaborators = $transactionRepository->listcollaborator();

        return $this->render('gestapp/transaction/add_collaborator/index.html.twig', [
            'listcollaborators' => $listCollaborators,
        ]);
    }
}
