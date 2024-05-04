<?php

namespace App\Controller\Gestapp;

use App\Entity\Admin\Notification;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Reco;
use App\Form\Gestapp\RecoType;
use App\Repository\Gestapp\RecoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;

#[Route('/gestapp/reco')]
class RecoController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_reco_index', methods: ['GET'])]
    public function index(RecoRepository $recoRepository): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true)
        {
            $recos = $recoRepository->findAll();
            return $this->render('gestapp/reco/index.html.twig', [
                'recos' => $recos,
            ]);
        }else{
            $recos = $recoRepository->findBy(['refEmployed' => $user->getId()]);
            return $this->render('gestapp/reco/index.html.twig', [
                'recos' => $recos,
            ]);
        }
    }

    #[Route('/new', name: 'op_gestapp_reco_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $reco = new Reco();
        $form = $this->createForm(RecoType::class, $reco, [
            'action' => $this->generateUrl('op_gestapp_reco_new') ,
            'method' => 'POST',
            'attr' => [
                'id' => 'formReco'
            ]

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reco->setRefEmployed($user);
            $reco->setOpenRecoAt(new \DateTime('now'));

            $entityManager->persist($reco);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
        }

        // view
        $view = $this->render('gestapp/reco/_form.html.twig', [
            'reco' => $reco,
            'form' => $form
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);

        //return $this->render('gestapp/reco/new.html.twig', [
        //    'reco' => $reco,
        //    'form' => $form,
        //]);

    }

    #[Route('/{id}', name: 'op_gestapp_reco_show', methods: ['GET'])]
    public function show(Reco $reco): Response
    {
        return $this->render('gestapp/reco/show.html.twig', [
            'reco' => $reco,
        ]);
    }


    #[Route('/{id}/edit', name: 'op_gestapp_reco_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $form = $this->createForm(RecoType::class, $reco, [
            'action' => $this->generateUrl('op_gestapp_reco_edit', ['id' => $reco->getId()]),
            'method' => 'POST',
            'attr' => [
                'id' => 'formReco'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statutReco = $form->get('statutReco')->getData();
            $step = $statutReco->getStep();
            if($step == 1){
                $OpenRecoAt = $reco->getOpenRecoAt();
                if(!$OpenRecoAt){
                    $reco->setOpenRecoAt(new \DateTime('now'));
                }
            }elseif($step == 2){
                $EmployedValidAt = $reco->getEmployedValidAt();
                if(!$EmployedValidAt){
                    $reco->setEmployedValidAt(new \DateTime('now'));
                }
            }elseif($step == 3){
                $RecoPublishedAt = $reco->getRecoPublishedAt();
                if(!$RecoPublishedAt){
                    $reco->setRecoPublishedAt(new \DateTime('now'));
                }
            }elseif($step == 4){
                $OnSaleAt = $reco->getOnSaleAt();
                if(!$OnSaleAt){
                    $reco->setOnSaleAt(new \DateTime('now'));
                }
            }elseif($step == 5){
                $RecoAbortedAt = $reco->getRecoAbortedAt();
                if(!$RecoAbortedAt){
                    $reco->setRecoAbortedAt(new \DateTime('now'));
                }
            }elseif($step == 6){
                $RecoFinishedAt = $reco->getRecoFinishedAt();
                if(!$RecoFinishedAt){
                    $reco->setRecoFinishedAt(new \DateTime('now'));
                }
            }elseif($step == 7){
                $PaidCommissionAt = $reco->getPaidCommissionAt();
                if(!$PaidCommissionAt){
                    $reco->setPaidCommissionAt(new \DateTime('now'));
                }
            }

            $notification = new Notification();
            $notification->setRefEmployed($user);
            $notification->setIsApi(0);
            $notification->setLog(array($reco));
            $notification->setClientHost($request->getClientIp());
            $entityManager->persist($notification);

            $entityManager->flush();

            if($hasAccess == true)
            {
                $recos = $recoRepository->findAll();
                return $this->json([
                    "code" => 200,
                    "message" => "Les modifications à la recommandations ont étés correctement apportées.",
                    'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig',[
                        'recos' => $recos
                    ])
                ],200);
            }else{
                $recos = $recoRepository->findBy(['refEmployed' => $user->getId()]);
                return $this->json([
                    "code" => 200,
                    "message" => "Les modifications à la recommandations ont étés correctement apportées.",
                    'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig',[
                        'recos' => $recos
                    ])
                ],200);
            }
        }

        // view
        $view = $this->render('gestapp/reco/_form.html.twig', [
            'reco' => $reco,
            'form' => $form
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);

    }

    #[Route('/{id}/edit/comm', name: 'op_gestapp_reco_edit_comm', methods: ['GET', 'POST'])]
    public function editComm(Request $request, Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($reco)
            ->add('commission')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {

        }

        // view
        $view = $this->render('gestapp/reco/include/_formComm.html.twig', [
            'reco' => $reco,
            'form' => $form
        ]);

        // return
        return $this->json([
            "code" => 200,
            'formView' => $view->getContent()
        ], 200);
    }

    #[Route('/{id}/AddProperty', name: 'op_gestapp_reco_addproperty', methods: ['GET', 'POST'])]
    public function AddProperty(Reco $reco, Property $property)
    {
        // Récupérer les information à transférer
        $propertyAddress = $reco->getPropertyAddress();
        $propertyComplement = $reco->getPropertyComplement();
        $propertyCity = $reco->getPropertyCity();
        $propertyZipcode = $reco->getPropertyZipcode();
        $propertyLat = $reco->getPropertyLat();
        $propertyLong = $reco->getPropertyLong();
        $propertyTypeReco = $reco->getTypeReco();
        $propertyFamily = $reco->getTypeFamily();
        $propertyTypeProperty = $reco->getTypeProperty();

        $property = new Property();
        $property->setAdress($propertyAddress);
        $property->setComplement($propertyComplement);
        $property->setZipcode($propertyZipcode);
        $property->setCity($propertyCity);
        $property->setCoordLong($propertyLong);
        $property->setCoordLat($propertyLat);

        $reco->setRefProperty($property);
    }

    #[Route('/{id}/step1', name: 'op_gestapp_reco_step1', methods: ['POST'])]
    public function step1(Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager)
    {
        $reco->setIsRead(1);
        $reco->setStatutReco('employed_valid');

        $entityManager->flush();

        $listrecos = $recoRepository->findAll();

        return $this->json([
            'code' => 200,
            'message' => "Un email va être envoyé à l'administration pour validation complête de la recommandation.",
            'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig', [
                'recos' => $listrecos
            ])
        ], 200);
    }

    #[Route('/{id}/step2', name: 'op_gestapp_reco_step2', methods: ['POST'])]
    public function step2(Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager)
    {
        $reco->setIsRead(0);
        $reco->setStatutReco('admin_valid');

        $entityManager->flush();

        $listrecos = $recoRepository->findAll();

        return $this->json([
            'code' => 200,
            'message' => "Un email a été envoyé au mandataire pour l'inscription de la recommandation dans la plateforme.",
            'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig', [
                'recos' => $listrecos
            ])
        ], 200);
    }

    #[Route('/{id}/step3', name: 'op_gestapp_reco_step3', methods: ['POST'])]
    public function step3(Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager)
    {
        $reco->setIsRead(0);
        $reco->setStatutReco('admin_valid');

        $entityManager->flush();

        $listrecos = $recoRepository->findAll();

        return $this->json([
            'code' => 200,
            'message' => "Un email a été envoyé au mandataire pour l'inscription de la recommandation dans la plateforme.",
            'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig', [
                'recos' => $listrecos
            ])
        ], 200);
    }

    #[Route('/{id}', name: 'op_gestapp_reco_delete', methods: ['POST'])]
    public function delete(Request $request, Reco $reco, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reco->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reco);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
    }
}
