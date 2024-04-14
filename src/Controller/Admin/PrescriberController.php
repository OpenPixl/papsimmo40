<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

class PrescriberController extends AbstractController
{
    #[Route('/admin/prescriber/{refemployed}', name: 'op_admin_prescriber_index', methods: ['GET'])]
    public function index(EmployedRepository $employedRepository, $refemployed): Response
    {
        $employed = $employedRepository->find($refemployed);
        $prescribers = $employedRepository->listPrescriber('["ROLE_PRESCRIBER"]', $employed);

        return $this->render('admin/employed/prescriber.html.twig', [
            'prescribers' => $prescribers,
        ]);
    }

    #[Route('/admin/prescriber/{id}/edit/ci', name: 'op_admin_prescriber_edit_ci', methods: ['GET'])]
    public function addCi(Employed $employed, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder($employed)
            ->add('ciFilename', FileType::class,[
                'label' => "Déposer le dossier PDF du compromis, le fichier ne doit pas dépasser 10Mo de taille",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10238k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $cipdf = $form->get('ciFilename')->getData();
            $ciFilename = $employed->getCiFileName();
            if($cipdf) {
                if ($ciFilename) {
                    $pathheader = $this->getParameter('employed_ci_directory') . '/' . $ciFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $originalFilename = pathinfo($cipdf->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '.' . $cipdf->guessExtension();
                try {
                    $cipdf->move(
                        $this->getParameter('transaction_acte_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $employed->setCiFileName($newFilename);
                $em->persist($employed);
                $em->flush();

                return $this->json([
                    'message' => 'Le document d\'identité est déposé sur le site'
                ], 200);
            }
        }

        // view
        $view = $this->render('gestapp/reco/_form.html.twig', [
            'employed' => $employed,
            'form' => $form
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

}