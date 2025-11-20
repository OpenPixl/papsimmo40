<?php

namespace App\Controller\Gestapp\Transaction;


use App\Controller\Gestapp\TransactionController;
use App\Entity\Gestapp\Transaction\Annulation;
use App\Form\Gestapp\Transaction\AnnulationType;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AnnulationController extends AbstractController
{
    public function __construct(
        public TransactionController $transactionController
    ){}

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    #[Route('/gestapp/transaction/annulation/{idTransaction}/new', name: 'op_gestapp_transaction_annulation_new', methods: ['GET','POST'])]
    public function new(Request $request, $idTransaction, TransactionRepository $transactionRepository, EntityManagerInterface $em, PropertyRepository $propertyRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $transaction = $transactionRepository->find($idTransaction);

        $annulation = new Annulation();
        $annulation->setTransaction($transaction);
        $form = $this->createForm(AnnulationType::class, $annulation, [
            'action' => $this->generateUrl('op_gestapp_transaction_annulation_new', [
                'idTransaction' => $idTransaction
            ]),
            'method' => 'POST',
            'attr' => [
                'id' => 'form_AnnulationTransaction'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            // les champs du formulaire sont valides
            if($form->isValid()) {
                // insertion du support de l'annulation
                // récupération de la référence du dossier pour construire le chemin vers le dossier Property
                $property = $propertyRepository->find($transaction->getProperty()->getId());
                $ref = explode("/", $property->getRef());
                $newref = $ref[0].'-'.$ref[1];
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";

                $supportFile = $form->get('supportFile')->getData();
                if($supportFile){
                    // Ajout de la nouvelle photo
                    $originalName = pathinfo($supportFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeName = 'ann-'.$slugger->slug($originalName);
                    $newName = $safeName . $supportFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        if (is_dir($pathdir)){
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            $supportFile->move(
                                $pathdir,
                                $newName
                            );
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $annulation->setSupportName($newName);
                }

                $annulation->setAuthor($user->getFirstName().' '.$user->getLastName());

                $transaction->setIsCancelled(true);
                $property->setIsTransaction(0);

                $hasAccess = $this->isGranted('ROLE_ADMIN');
                if($hasAccess == true){
                    $transactions = $transactionRepository->findAll();
                }else{
                    $transactions = $transactionRepository->findBy(['refEmployed' => $user->getId()]);
                }

                $em->persist($annulation);
                $em->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'l\'annulation de la vente est prise en compte dans le logiciel.',
                    'liste' => $this->renderView('gestapp/transaction/include/all_list_transactions.html.twig', [
                        'transactions' => $transactions,
                    ]),
                ], 200);
            }

            // Représentation du formulaire avec erreurs
            $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
                'annulation' => $annulation,
                'form' => $form
            ]);
            return $this->json([
                'code' => 422,
                'message' => 'Le formulaire présente une ou des erreurs.<br><span class="mt-1 mb-1 fw-semibold text-warning">'. implode(', ', $this->getFormErrors($form)). '</span><br>A vous de corriger celles-ci',
                'formView' => $view,
            ],200);
        }

        // Présentation du formulaire d'annulation vierge
        $view = $this->renderView('gestapp/transaction/annulation/_form.html.twig', [
            'annulation' => $annulation,
            'form' => $form
        ]);
        return $this->json([
            'code' => 200,
            'formView' => $view,
        ], 200);
    }
}
