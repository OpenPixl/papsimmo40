<?php

namespace App\Controller\Gestapp;

use App\Entity\Admin\Notification;
use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Entity\Gestapp\Reco;
use App\Form\Gestapp\Reco2Type;
use App\Form\Gestapp\RecoType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use App\Repository\Gestapp\choice\PropertyDefinitionRepository;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\choice\propertyFamilyRepository;
use App\Repository\Gestapp\choice\propertyRubricRepository;
use App\Repository\Gestapp\choice\propertyRubricssRepository;
use App\Repository\Gestapp\choice\StatutRecoRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use App\Repository\Gestapp\RecoRepository;
use App\Service\PropertyService;
use App\Service\QrcodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;

class RecoController extends AbstractController
{
    #[Route('/gestapp/reco/', name: 'op_gestapp_reco_index', methods: ['GET'])]
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
            $recos = $recoRepository->findBy(['refEmployed' => $user]);
            return $this->render('gestapp/reco/index.html.twig', [
                'recos' => $recos,
            ]);
        }
    }

    #[Route('/espace_partenaire', name: 'op_gestapp_reco_index_partenaire', methods: ['GET'])]
    public function index_prescripteur(RecoRepository $recoRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRESCRIBER');
        //$hasAccess = $this->isGranted('ROLE_PRESCRIBER');
        $user = $this->getUser();

        $recos = $recoRepository->findBy(['refPrescripteur' => $user->getId()]);
        $gains = 0;
        foreach ($recos as $reco){
            $gain = $reco->getCommission();
            $gains = $gains + $gain;
        }
        return $this->render('gestapp/reco/indexPrescriber.html.twig', [
            'recos' => $recos,
            'gains' => $gains
        ]);

    }

    #[Route('/newOnPublic', name: 'op_gestapp_reco_newonpublic', methods: ['GET', 'POST'])]
    public function newOnPublic(Request $request, EntityManagerInterface $entityManager, StatutRecoRepository $statutRecoRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRESCRIBER');
        $user = $this->getUser();
        $startReco = $statutRecoRepository->findOneBy(['id' => 1 ]);

        $reco = new Reco();
        $reco->setRefPrescripteur($user);
        $reco->setStatutReco($startReco);
        $reco->setRefEmployed($user->getReferent());
        $reco->setAnnounceFirstName($user->getFirstName());
        $reco->setAnnounceLastName($user->getLastName());
        $reco->setAnnounceEmail($user->getEmail());
        $reco->setAnnouncePhone($user->getGsm());
        $form = $this->createForm(Reco2Type::class, $reco, [
            'action' => $this->generateUrl('op_gestapp_reco_newonpublic') ,
            'method' => 'POST',
            'attr' => [
                'id' => 'formReco'
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reco->setOpenRecoAt(new \DateTime('now'));
            $reco->setAuthCustomer(0);
            $reco->setAuthRGPD(0);

            $entityManager->persist($reco);
            $entityManager->flush();

            return $this->redirectToRoute('op_gestapp_reco_index_prescripteur', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/reco/newonpublic.html.twig', [
            'reco' => $reco,
            'form' => $form,
        ]);

    }

    #[Route('/gestapp/reco/new', name: 'op_gestapp_reco_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $reco = new Reco();
        $reco->setRefEmployed($user->getReferent());
        $reco->setAnnounceFirstName($user->getFirstName());
        $reco->setAnnounceLastName($user->getLastName());
        $reco->setAnnounceEmail($user->getEmail());
        $reco->setAnnouncePhone($user->getGsm());
        $form = $this->createForm(RecoType::class, $reco, [
            'action' => $this->generateUrl('op_gestapp_reco_new') ,
            'method' => 'POST',
            'attr' => [
                'id' => 'formReco'
            ]

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    #[Route('/gestapp/reco/{id}', name: 'op_gestapp_reco_show', methods: ['GET'])]
    public function show(Reco $reco): Response
    {
        return $this->render('gestapp/reco/show.html.twig', [
            'reco' => $reco,
        ]);
    }

    #[Route('/gestapp/reco/public/{id}', name: 'op_gestapp_reco_showonpublic', methods: ['GET'])]
    public function showonpublic(Reco $reco): Response
    {
        return $this->render('gestapp/reco/show_on_public.html.twig', [
            'reco' => $reco,
        ]);
    }


    #[Route('/gestapp/reco/{id}/edit', name: 'op_gestapp_reco_edit', methods: ['GET', 'POST'])]
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

    #[Route('/gestapp/reco/{id}/edit/comm', name: 'op_gestapp_reco_edit_comm', methods: ['GET', 'POST'])]
    public function editComm(Request $request, Reco $reco, RecoRepository $recoRepository, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $form = $this->createFormBuilder($reco)
            ->setAction($this->generateUrl('op_gestapp_reco_edit_comm', ['id' => $reco->getId()]))
            ->setMethod('POST')
            ->add('commission')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($reco);
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

    #[Route('/gestapp/reco/{id}/AddProperty', name: 'op_gestapp_reco_addproperty', methods: ['GET', 'POST'])]
    public function AddProperty(
        Reco $reco,
        PropertyService $propertyService,
        PropertyRepository $propertyRepository,
        propertyFamilyRepository $familyRepository,
        propertyRubricRepository $rubricRepository,
        propertyRubricssRepository $rubricssRepository,
        EmployedRepository $employedRepository,
        EntityManagerInterface $em,
        QrcodeService $qrcodeService
    )
    {
        // Récupérer les information à transférer
        $idemployed = $reco->getRefEmployed()->getId();
        //dd($idemployed);
        $employed = $employedRepository->find($idemployed);
        //dd($employed);

        $client = $propertyService->addCustomer($reco);

        // préparation des complements au bien
        $complement = $propertyService->getNewComplement();
        // création d'une fiche Publication
        $publication = $propertyService->getPublication();
        if ($reco->getTypeReco() === "vente" ){
            if($reco->getTypeProperty() === "appartement"){
                $family = $familyRepository->find(8);
                $rubric = $rubricRepository->find(38);
                $rubricss = $rubricssRepository->find(69);
            }elseif($reco->getTypeProperty() === "maison"){
                $family = $familyRepository->find(8);
                $rubric = $rubricRepository->find(48);
                $rubricss = $rubricssRepository->find(69);
            }elseif($reco->getTypeProperty() === "local_commercial"){
                $family = $familyRepository->find(4);
                $rubric = $rubricRepository->find(9);
                $rubricss = $rubricssRepository->find(7);
            };
        }elseif($reco->getTypeReco() === "location" ){
            if($reco->getTypeProperty() === "appartement"){
                $family = $familyRepository->find(5);
                $rubric = $rubricRepository->find(10);
                $rubricss = null;
            }elseif($reco->getTypeProperty() === "maison"){
                $family = $familyRepository->find(5);
                $rubric = $rubricRepository->find(19);
                $rubricss = null;
            }elseif($reco->getTypeProperty() === "local_commercial"){
                $family = $familyRepository->find(4);
                $rubric = $rubricRepository->find(8);
                $rubricss = $rubricssRepository->find(7);
            };
        }

        $date = new \DateTime();
        $lastproperty = $propertyRepository->findOneBy([], ['id'=>'desc']);             // Récupération de la dernière propriété enregistrée
        if($lastproperty){
            $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
            $RefMandat = 0;                           // construction du numéro de mandat obligatoire
        }else{
            $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
            $RefMandat = 22;
        }

        $property = new Property();
        $property->setAnnonceDown('<p class="mb-0">Contact : '.$reco->getRefEmployed()->getGsm().' ou '. $reco->getRefEmployed()->getEmail() .'</p><p>Les informations sur les risques auxquels, ce bien est exposé sont disponibles sur le site Géorisques : www.georisques.gouv.fr</p>');
        $property->setFamily($family);
        $property->setRubric($rubric);
        $property->setRubricss($rubricss);
        $property->setPiece(0);
        $property->setRoom(0);
        if(!$reco->getTitle()){
            $property->setName('bien issu d\'une recommandation');
        }else{
            $property->setName($reco->getTitle());
        }

        if(!$lastproperty){
            $lastRefNum = 1;
            $property->setRefnumdate($refNumDate);
            $property->setReflastnumber($lastRefNum);
        }else{
            $lastRefDate = $lastproperty->getRefnumdate();
            if($lastRefDate == $refNumDate){
                $lastRefNum = $lastproperty->getReflastnumber()+1;
                $property->setRefnumdate($refNumDate);
                $property->setReflastnumber($lastRefNum);
            }else{
                $lastRefNum = 1;
                $property->setRefnumdate($refNumDate);
                $property->setReflastnumber($lastRefNum);
            }
        }
        $property->setRef($refNumDate.'-'.$lastRefNum);
        $property->setSurfaceHome(0);
        $property->setSurfaceLand(0);
        $property->setPrice(0);
        $property->setHonoraires(0);
        $property->setPriceFai(0);
        $property->setRent(0);
        $property->setRentCharge(0);
        $property->setRentChargeModsPayment(1);
        $property->setWarrantyDeposit(0);
        $property->setDiagChoice('obligatoire');
        $property->setDiagDpe(0);
        $property->setDiagGes(0);
        $property->setDpeEstimateEnergyUp(0);
        $property->setDpeEstimateEnergyDown(0);
        $property->setRefEmployed($employed);
        $property->setOptions($complement);
        $property->setPublication($publication);
        $property->setIsIncreating(1);
        $property->setRefMandat($RefMandat);
        $property->setIsNomandat(1);
        $property->setMandatAt(new \DateTime('now'));
        $property->setIsWithoutExclusivity(1);
        $property->setProjet('VH');
        $property->setAdress($reco->getPropertyAddress());
        $property->setComplement($reco->getPropertyComplement());
        $property->setZipcode($reco->getPropertyZipcode());
        $property->setCity($reco->getPropertyCity());
        $property->setCoordLong($reco->getPropertyLong());
        $property->setCoordLat($reco->getPropertyLat());
        $property->addCustomer($client);

        $qrCode = $qrcodeService->qrcodeOneProperty($property);
        $property->setQrcodeUrl($qrCode);

        $reco->setRefProperty($property);
        $em->persist($property);
        $em->flush();

        return $this->redirectToRoute('op_gestapp_property_show', [
            'id' => $property->getId()
        ]);
    }

    #[Route('/gestapp/reco/{id}/step1', name: 'op_gestapp_reco_step1', methods: ['POST'])]
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

    #[Route('/gestapp/reco/{id}/step2', name: 'op_gestapp_reco_step2', methods: ['POST'])]
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

    #[Route('/gestapp/reco/{id}/step3', name: 'op_gestapp_reco_step3', methods: ['POST'])]
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

    #[Route('/gestapp/reco/{id}', name: 'op_gestapp_reco_delete', methods: ['POST'])]
    public function delete(Request $request, Reco $reco, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reco->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reco);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_reco_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/gestapp/reco/{id}/del', name: 'op_gestapp_reco_del', methods: ['POST'])]
    public function del(Request $request, Reco $reco, EntityManagerInterface $entityManager, RecoRepository $recoRepository, PropertyRepository $propertyRepository): Response
    {
        $entityManager->remove($reco);
        $entityManager->flush();

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true)
        {
            $recos = $recoRepository->findAll();
        }else{
            $recos = $recoRepository->findBy(['refEmployed' => $user->getId()]);
        }

        return $this->json([
            'code' => 200,
            'message' => "La recommandation a été correctement supprimée de la base de données.",
            'liste' => $this->renderView('gestapp/reco/include/_liste.html.twig', [
                'recos' => $recos
            ])
        ], 200);
    }

}
