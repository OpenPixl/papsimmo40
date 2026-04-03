<?php

namespace App\Controller\Gestapp\Transaction;

use App\Controller\Gestapp\TransactionController;
use App\Entity\Gestapp\Transaction;
use App\Entity\Gestapp\Transaction\Acte;
use App\Form\Gestapp\Transaction\ActeFormType;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\Transaction\ActeRepository;
use App\Repository\Gestapp\TransactionRepository;
use App\Service\EmailService;
use App\Service\PropertyService;
use App\Service\transactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActeController extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        public EmailService $emailService,
        public transactionService $transactionService,
        private readonly EntityManagerInterface $entityManager,
    ){
    }

    public function access(Transaction $transaction){
        $user = $this->getUser();
        $permission = 'read'; // Valeur par défaut
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $access = 'admin';
        } elseif ($this->isGranted('ROLE_EMPLOYED')) {
            if ($transaction->getRefEmployed() === $user) {
                $access = 'edit';
            } else {
                $access = 'read';
            }
        }
        return $access;
    }

    #[Route('/gestapp/transaction/acte', name: 'app_gestapp_transaction_acte_index')]
    public function index(): Response
    {
        $actes = $this->entityManager->getRepository(Acte::class)->findAll();
        return $this->render('gestapp/transaction/acte/index.html.twig', [
            'actes' => $actes,
        ]);
    }

    #[Route('/gestapp/transaction/acte/new/{idtransaction}', name: 'app_gestapp_transaction_acte_new')]
    public function new(
        Request $request,
        PropertyRepository $propertyRepository,
        TransactionRepository $transactionRepository, $idtransaction): Response
    {
        $user = $this->getUser();
        $transaction = $transactionRepository->find($idtransaction);
        $access = $this->access($transaction);

        $actes = $this->entityManager->getRepository(Acte::class)->findBy(['transaction' => $idtransaction]);
        $acte = new Acte();
        $form = $this->createForm(ActeFormType::class, $acte, [
            'action' => $this->generateUrl('app_gestapp_transaction_acte_new', ['idtransaction' => $idtransaction]),
            'method' => 'POST',
            'attr' => [
                'id' => 'formActe_addAvenant'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid())
            {
                // *******************************************
                // Bloc "Ajout du Fichier"
                // *******************************************

                // récupération de la référence du dossier pour construire le chemin vers le dossier Property
                $property = $propertyRepository->find($transaction->getProperty()->getId());
                $ref = explode("/", $property->getRef());
                $dir = $ref[0].'-'.$ref[1];
                // récupération du nom de repertoire du bien en lien avec les photos
                $dir = $this->propertyService->getDir($property);
                $pathdir = $this->getParameter('property_doc_directory')."/".$dir."/documents/";

                $acteFile = $form->get('acteFile')->getData();
                $acteName = $form->get('acteName')->getData();
                //dd($acteName->name);
                if($acteFile){
                    $refMandat = $this->propertyService->getMandat($property);
                    $newFilename = $acteName->name.'-m'.$refMandat.'.'.$acteFile->guessExtension();

                    try {
                        if (is_dir($pathdir)){
                            $acteFile->move(
                                $pathdir,
                                $newFilename
                            );
                        }else{
                            // Création du répertoire s'il n'existe pas.
                            mkdir($pathdir."/", 0775, true);
                            // Déplacement de la photo
                            $acteFile->move(
                                $pathdir,
                                $newFilename
                            );
                        }

                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $acte->setActeName($acteName);
                    $acte->setPath($pathdir);
                    $acte->setActeFilename($newFilename);
                }

                $acte->setTransaction($transaction);

                $this->entityManager->persist($acte);
                $this->entityManager->flush();

                return $this->json([
                    "code" => 200,
                    "message" => "Nouvel Avenant ajouter au dossier",
                ], 200);
            }

            return $this->json([
                "code" => 400,
                "message" => "Formulaire invalide",
            ], 400);

        }

        $view = $this->renderView('gestapp/transaction/acte/new.html.twig', [
            'form' => $form->createView(),
            'actes' => $actes,
            'acte' => $acte,
        ]);

        return $this->json([
            "code" => 200,
            "message" => "Actes retrieved successfully",
            "formView" => $view,
        ], 200);
    }
}
