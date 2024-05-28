<?php

namespace App\Controller\Gestapp\Transaction;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\AddCollTransac;
use App\Form\Gestapp\Transaction\addCollaboratorInvoiceType;
use App\Form\Gestapp\Transaction\addCollaboratorType;
use App\Form\Gestapp\Transaction\AddCollTransacType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\Transaction\AddCollTransacRepository;
use App\Repository\Gestapp\TransactionRepository;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/gestapp/transaction/addcollaborator/{refEmployed}/addinvoice/{idTransac}', name: 'op_gestapp_transaction_addcollaborator_addinvoice')]
    public function AddInvoice(
        $refEmployed,
        $idTransac,
        Request $request,
        EntityManagerInterface $em,
        PropertyService $propertyService,
        AddCollTransacRepository $addCollTransacRepository,
        TransactionRepository $transactionRepository,
        SluggerInterface $slugger
    )
    : Response
    {
        //dd($refEmployed, $idTransac);
        $transaction = $transactionRepository->find($idTransac);
        $addColl = $addCollTransacRepository->findOneBy(['refTransac' => $transaction, 'refemployed' => $this->getUser()]);

        $refDir = $propertyService->getDir($transaction->getProperty());

        $form = $this->createForm(AddCollaboratorInvoiceType::class, $addColl,[
            'action' => $this->generateUrl('op_gestapp_transaction_addcollaborator_addinvoice', [
                'refEmployed' => $refEmployed,
                'idTransac' => $idTransac
            ]),
            'attr' => [
                'id' => 'FormAddcollaboratorInvoice',
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $invoicepdf = $form->get('invoicePdfFilename')->getData();
            $invoicePdfName = $transaction->getinvoicePdfFilename();
            if($invoicepdf){
                // suppression lors de la mise à jour du fichier
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
                $addColl->setInvoicePdfFilename($newFilename);
                $em->persist($addColl);
                $em->flush();
            }

            return $this->json([
                "code" => 200,
                "message" => "Le collaborateur à été ajouté",
                'rowInvoice' => $this->renderView('gestapp/transaction/add_collaborator/addInvoice.html.twig',[
                    'refemployed' => $refEmployed,
                    'idTransac' => $idTransac
                ])
            ],200);
        }

        return $this->render('gestapp/transaction/add_collaborator/addInvoice.html.twig',[
            'form' => $form,
            'addColl' => $addColl
        ]);
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
