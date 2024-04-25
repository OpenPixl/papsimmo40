<?php

namespace App\Controller\Gestapp\Transaction;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\AddCollTransac;
use App\Form\Gestapp\Transaction\addCollaboratorType;
use App\Form\Gestapp\Transaction\AddCollTransacType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\Transaction\AddCollTransacRepository;
use App\Repository\Gestapp\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCollaboratorController extends AbstractController
{
    #[Route('/gestapp/transaction/addcollaborator/{id}/listbytransac', name: 'op_gestapp_transaction_add_collaborator_listbytransac')]
    public function listByTransac(Transaction $transaction, AddCollTransacRepository $addCollTransacRepository): Response
    {
        $listCollaborators = $addCollTransacRepository->listcollTransac($transaction);

        return $this->render('gestapp/transaction/add_collaborator/index.html.twig', [
            'listcollaborators' => $listCollaborators,
        ]);
    }

    #[Route('/gestapp/transaction/addcollaborator/{idEmployed}/listbyemployed', name: 'op_gestapp_transaction_add_collaborator_listbyemployed')]
    public function listByEmployed($idEmployed, EmployedRepository $employedRepository, AddCollTransacRepository $addCollTransacRepository): Response
    {
        $listtransacs = $addCollTransacRepository->listcollEmployed($idEmployed);

        //dd($listtransacs);

        return $this->render('gestapp/transaction/include/_coliste.html.twig', [
            'listtransacs' => $listtransacs,
        ]);
    }

    #[Route('/gestapp/transaction/addcollaborator/add/{idtransac}', name: 'op_gestapp_transaction_addcollaborator_add')]
    public function addColl($idtransac, Request $request, EntityManagerInterface $entityManager, AddCollTransacRepository $addCollTransacRepository, TransactionRepository $transactionRepository): Response
    {
        $addCollTransac = new AddCollTransac();
        $form = $this->createForm(AddCollaboratorType::class, $addCollTransac,[
            'action' => $this->generateUrl('op_gestapp_transaction_addcollaborator_add', ['idtransac' => $idtransac]),
            'attr' => [
                'id' => 'FormAddcollaborator',
            ]
        ]);
        $form->handleRequest($request);

        $transac = $transactionRepository->find($idtransac);

        if ($form->isSubmitted() && $form->isValid()) {
            $addCollTransac->setRefTransac($transac);
            $entityManager->persist($addCollTransac);
            $entityManager->flush();



            $listCollaborators = $addCollTransacRepository->listcollTransac($idtransac);

            return $this->json([
                "code" => 200,
                "message" => "Le collaborateur à été ajouté",
                'listCollaborator' => $this->renderView('gestapp/transaction/add_collaborator/index.html.twig',[
                    'listcollaborators' => $listCollaborators
                ])
            ],200);
        }

        // view
        $view = $this->render('gestapp/transaction/add_collaborator/add.html.twig', [
            'addCollTransac' => $addCollTransac,
            'form' => $form,
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/gestapp/transaction/addcollaborator/{id}/suppr/{idtransac}', name: 'op_gestapp_transaction_addcollaborator_suppr')]
    public function supprCollaborator(AddCollTransac $addCollTransac, EntityManagerInterface $em, AddCollTransacRepository $addCollTransacRepository, $idtransac)
    {
        $em->remove($addCollTransac);
        $em->flush();

        $listCollaborators = $addCollTransacRepository->listcollTransac($idtransac);

        return $this->json([
            "code" => 200,
            "message" => "Le collaborateur à été retiré.",
            'listCollaborator' => $this->renderView('gestapp/transaction/add_collaborator/index.html.twig',[
                'listcollaborators' => $listCollaborators
            ])
        ],200);
    }

}
