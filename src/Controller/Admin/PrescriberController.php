<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Form\Admin\PrescriberType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\RecoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

    #[Route('/admin/prescriber/{id}/edit/ci', name: 'op_admin_prescriber_edit_ci', methods: ['GET', 'POST'])]
    public function addCi(
        Employed $employed,
        RecoRepository $recoRepository,
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em)
    {
        $form = $this->createForm(PrescriberType::class, $employed, [
            'action' => $this->generateUrl('op_admin_prescriber_edit_ci', [
                'id' => $employed->getId()
            ]),
            'attr' => [
                'id' => 'formPrescriber'
                ]
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            //dd($form->isSubmitted(), $form->isValid());
            $cipdf = $form->get('ciFileName')->getData();
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
            }

            $em->persist($employed);
            $em->flush();

            $recos = $recoRepository->findBy(['refEmployed' => $employed]);

            return $this->json([
                'message' => 'Le document d\'identité est déposé sur le site',
                'liste' =>  $this->renderView('gestapp/reco/include/_liste.html.twig',[
                    'recos' => $recos
                ])
            ], 200);
        }

        // view
        $view = $this->render('admin/employed/include/_formPrescriber.html.twig', [
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