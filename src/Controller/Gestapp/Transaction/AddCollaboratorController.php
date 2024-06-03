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
            'transaction' => $transaction,
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

    #[Route('/gestapp/transaction/addcollaborator/add/{idtransac}', name: 'op_gestapp_transaction_addcollaborator_add', methods: ['POST', 'GET'])]
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
                ]),

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

    #[Route('/gestapp/transaction/addcollaborator/{refEmployed}/addinvoice/{idTransac}', name: 'op_gestapp_transaction_addcollaborator_addinvoice', methods: ['POST', 'GET'])]
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
        $user = $this->getUser();
        //dd($refEmployed, $idTransac);
        $transaction = $transactionRepository->find($idTransac);
        //dd($transaction);
        $addColl = $addCollTransacRepository->findOneBy(['refTransac' => $idTransac, 'refemployed' => $refEmployed]);

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
                    $pathheader = $this->getParameter('property_doc_directory')."/".$refDir."/documents/".$invoicePdfName;
                    // On vérifie si le document
                    if(file_exists($pathheader)){
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($invoicepdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'fhc-'.$user->getLastName().'_'.$user->getFirstName().'.'.$invoicepdf->guessExtension();
                try {
                    $invoicepdf->move(
                        $this->getParameter('property_doc_directory')."/".$refDir."/documents/",
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
                "message" => "La facture a été correctement déposée",
            ],200);
        }

        // view
        $view = $this->render('gestapp/transaction/add_collaborator/addInvoice.html.twig', [
            'addColl' => $addColl,
            'form' => $form
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);

        //return $this->render('gestapp/transaction/add_collaborator/addInvoice.html.twig',[
        //    'form' => $form,
        //    'addColl' => $addColl
        //]);
    }

    #[Route('/gestapp/transaction/addcollaborator/{id}/supprinvoice', name: 'op_gestapp_transaction_addcollaborator_supprinvoice', methods: ['POST', 'GET'])]
    public function SupprInvoice(
        AddCollTransac $addCollTransac,
        Request $request,
        EntityManagerInterface $em,
        PropertyService $propertyService,
        AddCollTransacRepository $addCollTransacRepository,
        TransactionRepository $transactionRepository,
        SluggerInterface $slugger
    )
    : Response
    {
        $user = $this->getUser();
        $iduser = $user->getId();

        // securite : controle que l'user est le bon
        if ($iduser == $addCollTransac->getRefemployed()->getId())
        {
            // supprimer le fichier réel dans le dossier
            $transaction = $transactionRepository->find($addCollTransac->getRefTransac());
            $refDir = $propertyService->getDir($transaction->getProperty());
            $invoicePdfName = $addCollTransac->getInvoicePdfFilename();
            if($invoicePdfName){
                $pathheader = $this->getParameter('property_doc_directory')."/".$refDir."/documents/".$invoicePdfName;
                // On vérifie si le document
                if(file_exists($pathheader)){
                    unlink($pathheader);
                }
            }

            // alimenter le champs facture par null
            $addCollTransac->setInvoicePdfFilename(null);
            $addCollTransac->setInvoicePdfExt(null);
            $addCollTransac->setInvoicePdfSize(null);
            $em->flush();

            return $this->json([
                "code" => 200,
                "message" => 'La facture a été correctement supprimée de la base.',
                "row" => $this->renderView('gestapp/transaction/include/block/_rowinvoicesPdf.html.twig', [
                    'transaction' => $transaction
                ])
            ], 200);
        }else{
            // return
            return $this->json([
                "code" => 200,
                "message" => 'Vous n\'avez pas la permission de supprimer la facture'
            ], 200);
        }
    }

    #[Route('/gestapp/transaction/addcollaborator/{id}/suppr/{idtransac}', name: 'op_gestapp_transaction_addcollaborator_suppr')]
    public function supprCollaborator(AddCollTransac $addCollTransac, EntityManagerInterface $em, AddCollTransacRepository $addCollTransacRepository, $idtransac)
    {
        $transaction = $addCollTransac->getRefTransac();

        $em->remove($addCollTransac);
        $em->flush();

        $listCollaborators = $addCollTransacRepository->listcollTransac($idtransac);

        return $this->json([
            "code" => 200,
            "message" => "Le collaborateur à été retiré.",
            'listCollaborator' => $this->renderView('gestapp/transaction/add_collaborator/index.html.twig',[
                'listcollaborators' => $listCollaborators
            ]),
            "row" => $this->renderView('gestapp/transaction/include/block/_rowinvoicesPdf.html.twig', [
                'transaction' => $transaction
            ])
        ],200);
    }
}
