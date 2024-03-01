<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\Property\AddMandatType;
use App\Form\Gestapp\PropertyAvenantType;
use App\Form\Gestapp\PropertyEndMandatType;
use App\Form\Gestapp\PropertyImageType;
use App\Form\Gestapp\PropertyStep1Type;
use App\Form\Gestapp\PropertyStep2Type;
use App\Form\Gestapp\PropertyType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\CadasterRepository;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use App\Repository\Gestapp\choice\PropertyDefinitionRepository;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\choice\propertyFamilyRepository;
use App\Repository\Gestapp\choice\propertyRubricRepository;
use App\Repository\Gestapp\choice\propertyRubricssRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Service\ArchivePropertyService;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/gestapp/property')]
class PropertyController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_property_index', methods: ['GET'])]
    public function index(
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator,
        Request $request,
        PublicationRepository $publicationRepository,
        CadasterRepository $cadasterRepository,
        PhotoRepository $photoRepository,
        ComplementRepository $complementRepository,
        ArchivePropertyService $archiveProperty,
        PropertyService $propertyService,
        EntityManagerInterface $em
    ): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        if($hasAccess == true){
            // dans ce cas, nous listons toutes les propriétés de chaque utilisateurs
            $data = $propertyRepository->listAllProperties();

            $expireAtOut = [];
            // tri des bien avec date de fin de mandat inférieur à aujourd'hui
            foreach ($data as $d){
                $dateEndMandat = $d['dateEndmandat'];
                $idpro = $propertyRepository->find($d['id']);

                if($dateEndMandat !== null){
                    $propertyService->expireAtOut($idpro, $publicationRepository, $em);
                    array_push($expireAtOut, $d['id']);
                }
            }
            //dd($expireAtOut);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('gestapp/property/index.html.twig', [
                'properties' => $properties,
                'user' => $user,
                'expireAtOut' => count($expireAtOut)
            ]);
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            // tri des bien avec date de fin de mandat inférérieur à aujourd'hui
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('gestapp/property/index.html.twig', [
                'properties' => $properties,
                'user' => $user
            ]);
        }
    }

    /**
     * Affiche tous les biens immobiliers en location dans la section adaptée".
     */
    #[Route('/allRentCommerce', name: 'op_gestapp_properties_allrentcommerce', methods: ['GET'])]
    public function AllRentCommerce(
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        // Récupération de la page si elle existe
        $page = $request;
        $data = $propertyRepository->AllCommercesRent();
        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            24
        );

        return $this->render('webapp/page/property/allproperties.html.twig', [
            'properties' => $properties,
            'page' => $request->query->getInt('page', 1),
        ]);
    }

    /**
     * Affiche tous les biens immobiliers en location dans la section adaptée".
     */
    #[Route('/allSaleCommerce', name: 'op_gestapp_properties_allsalecommerce', methods: ['GET'])]
    public function AllSaleCommerce(
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        // Récupération de la page si elle existe
        $page = $request;
        $data = $propertyRepository->AllCommercesSale();
        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            24
        );

        return $this->render('webapp/page/property/allproperties.html.twig', [
            'properties' => $properties,
            'page' => $request->query->getInt('page', 1),
        ]);
    }

    #[Route('/propertyDiffusion', name: 'op_gestapp_property_diffusion', methods: ['GET']) ]
    public function propertyDiffusion(PropertyRepository $propertyRepository)
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true) {
            $listProperties = $propertyRepository->listPublication();
        }else{
            $listProperties = $propertyRepository->listPublicationEmployed($user->getId());
        }

        return $this->json([
            'code' => 200,
            'message' => 'affichage de la liste',
            'listdiffusion' => $this->renderView('gestapp/property/_listdiffusion.html.twig', [
                'listproperties' => $listProperties
            ])
        ],200);
    }

    #[Route('/listarchived', name: 'op_gestapp_property_listarchived', methods: ['GET'])]
    public function listArchived(
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        CadasterRepository $cadasterRepository,
        PublicationRepository $publicationRepository,
        ComplementRepository $complementRepository,
        ArchivePropertyService $archivePropertyService,
        PaginatorInterface $paginator,
        Request $request)
    {
        // dans ce cas, nous listons toutes les propriétés de chaque utilisateurs
        $properties = $propertyRepository->listAllPropertiesArchived();
        $countArchivedAtExpired = 0;
        foreach($properties as $p)
        {
            $now = new \DateTime('now');
            $property = $propertyRepository->find($p['id']);
            $dateArchivedAt = $property->getArchivedAt();
            $archivedAtExpired = [];
            if($now >= $dateArchivedAt){
                array_push($archivedAtExpired, $property->getId());
                $archivePropertyService->DelArchived($property, $photoRepository, $cadasterRepository, $publicationRepository, $complementRepository);
            }
            if(count($archivedAtExpired) > 0){
                $countArchivedAtExpired = count($archivedAtExpired);
            }
            //$archiveProperty->onArchive($propertyRepository);
        }
        $data = $propertyRepository->listAllPropertiesArchived();
        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées.",
            'listarchived' => $this->renderView('gestapp/property/_listarchived.html.twig',[
                'properties' => $data
            ]),
            'expiredArchived' => $countArchivedAtExpired
        ], 200);
    }

    #[Route('/getlistmandats', name:'op_gestapp_property_getlastmandat', methods: ['GET'])]
    public function getLastMandat(PropertyRepository $propertyRepository){

        $properties = $propertyRepository->findBy(['isArchived'=> 0]);             // Récupération de la dernière propriété enregistrée

        $listMandats = array();
        foreach ($properties as $property)
        {
            $refMandat = $property->getRefMandat();
            array_push($listMandats, $refMandat);
        }

        return $this->json([
            'code'=> 200,
            'message' => "Le bien a été archivé sur le site.",
            'listmandats' => $listMandats
        ], 200);
    }

    #[Route('/inCreating', name: 'op_gestapp_property_inCreating', methods: ['GET'])]
    public function inCreating(PropertyRepository $propertyRepository): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        if($hasAccess == true){
            $properties = $propertyRepository->listAllPropertiesIncreating();
            //dd($properties);
            return $this->render('gestapp/property/increating.html.twig', [
                'properties' => $properties,
            ]);
        }
        else{
            return $this->render('gestapp/property/increating.html.twig', [
                'properties' => $propertyRepository->findBy(['refEmployed'=>$user->getId(), 'isIncreating' => 1]),
            ]);
        }
    }

    #[Route('/new', name: 'op_gestapp_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyRepository $propertyRepository): Response
    {
        $user = $this->getUser()->getId();

        $property = new Property();
        $property->setRefEmployed($user);
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = str_replace(array( "\n", "\r" ), array( '', '' ), html_entity_decode($property['annonce']) );
            $annonceSlug = substr(strip_tags($data, '<br>'), 0, 59);
            $property->setAnnonceSlug($annonceSlug);
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/duplicate/{id}', name:'op_gestapp_property_duplicate', methods: ['GET', 'POST'])]
    public function duplicate(
        Property $property,
        ComplementRepository $complementRepository,
        PublicationRepository $publicationRepository,
        PropertyRepository $propertyRepository,
        PropertyService $propertyService
    )
    {
        // Vérification si property été dupliqué
        $refs = $propertyService->getRefs($property, $propertyRepository);

        // Clonage des options de la propriété
        $complement = $property->getOptions();
        $dupcomplement = clone $complement;
        $complementRepository->add($dupcomplement);

        // Clonage des publications de la propriété
        $publication = $property->getPublication();
        $dupublication = clone $publication;
        $dupublication->setIsWebpublish(0);
        $dupublication->setIsPublishParven(0);
        $dupublication->setIsPublishMeilleur(0);
        $dupublication->setIsPublishleboncoin(0);
        $dupublication->setIsPublishseloger(0);
        $publicationRepository->add($dupublication);

        // Clonage de la propriété
        $dupproperty = clone $property;

        // Numéro de duplicata
        $dupproperty->setRef($refs['ref']);
        $dupproperty->setDupMandat($refs['dup']);
        $dupproperty->setOptions($dupcomplement);
        $dupproperty->setPublication($dupublication);
        $dupproperty->setIsIncreating(0);

        //dd($dupproperty);
        $propertyRepository->add($dupproperty);

        return $this->render('gestapp/property/show.html.twig', [
            'property' => $dupproperty,
            'complement' => $dupcomplement->getId(),
            'publication' => $dupproperty->getPublication(),
        ]);

    }

    #[Route('/transferate/{id}', name:'op_gestapp_property_transferate', methods: ['GET', 'POST'])]
    public function transferate(
        Property $property,
        ComplementRepository $complementRepository,
        PublicationRepository $publicationRepository,
        PropertyRepository $propertyRepository,
        EmployedRepository $employedRepository,
        PropertyService $propertyService,
        PhotoRepository $photoRepository,
        Request $request
    )
    {
        $idemployed = $request->request->get('SelectEmployed');
        $employed = $employedRepository->find($idemployed);

        // Vérification si property été dupliqué
        $refs = $propertyService->getRefs($property, $propertyRepository);

        // Clonage des options de la propriété
        $complement = $property->getOptions();
        $dupcomplement = clone $complement;
        $complementRepository->add($dupcomplement);

        // Clonage des publications de la propriété
        $publication = $property->getPublication();
        $dupublication = clone $publication;
        $dupublication->setIsWebpublish(0);
        $dupublication->setIsPublishParven(0);
        $dupublication->setIsPublishMeilleur(0);
        $dupublication->setIsPublishleboncoin(0);
        $dupublication->setIsPublishseloger(0);
        $publicationRepository->add($dupublication);

        // Clonage de la propriété
        $dupproperty = clone $property;

        // Numéro de duplicata
        $dupproperty->setRef($refs['ref']);
        $dupproperty->setRefEmployed($employed);
        $dupproperty->setDupMandat($refs['dup']);
        $dupproperty->setOptions($dupcomplement);
        $dupproperty->setPublication($dupublication);
        $dupproperty->setIsIncreating(0);

        //dd($dupproperty);
        $propertyRepository->add($dupproperty);

        return $this->redirectToRoute('op_gestapp_property_index');
    }

    #[Route('/add/{isNomandat}/{refMandat}/{destination}', name:'op_gestapp_property_add', methods: ['GET', 'POST'])]
    public function add(
        PropertyRepository $propertyRepository,
        EmployedRepository $employedRepository,
        ComplementRepository $complementRepository,
        PublicationRepository $publicationRepository,
        PropertyEquipementRepository $propertyEquipementRepository,
        OtherOptionRepository $otherOptionRepository,
        PropertyDefinitionRepository $propertyDefinitionRepository,
        propertyFamilyRepository $familyRepository,
        propertyRubricRepository $rubricRepository,
        propertyRubricssRepository $rubricssRepository,
        $isNomandat,
        $refMandat,
        $destination
        )
    {
        // Récupération du collaborateur
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        // préparation des complements au bien
        $complement = new Complement();
        $complement->setTerrace(0);
        $complement->setWashroom(0);
        $complement->setBathroom(0);
        $complement->setWc(0);
        $complement->setBalcony(0);
        $complement->setPropertyTax(0);
        $complement->setCoproprietyTaxe(0);
        $complement->setLevel(0);
        $complement->addPropertyEquipment($propertyEquipementRepository->findOneBy([], ['id'=>'ASC']));
        $complement->addPropertyOtheroption($otherOptionRepository->findOneBy([], ['id'=>'ASC']));
        $complementRepository->add($complement);
        // création d'une fiche Publication
        $publication = new Publication();
        $publicationRepository->add($publication);
        // ---
        // Contruction de la référence pour chaque propriété
        // ---
        $date = new \DateTime();
        $lastproperty = $propertyRepository->findOneBy([], ['id'=>'desc']);             // Récupération de la dernière propriété enregistrée
        if($lastproperty){
            $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
            $RefMandat = $refMandat;                           // construction du numéro de mandat obligatoire
        }else{
            $refNumDate = $date->format('Y').'/'.$date->format('m').$date->format('d').$date->format('s');        // contruction de la première partie de référence
            $RefMandat = 22;
        }

        $family = $familyRepository->find(substr($destination, 0,-1));
        $rubric = $rubricRepository->find(substr($destination, -1,1));
        $rubricss = $rubricssRepository->find(69);       // Création de l'entité Property

        $property = new Property();
        $property->setFamily($family);
        $property->setRubric($rubric);
        $property->setRubricss($rubricss);
        $property->setPiece(0);
        $property->setRoom(0);
        $property->setName('Nouveau bien');
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
        $property->setIsNomandat($isNomandat);
        $property->setMandatAt(new \DateTime('now'));
        $property->setIsWithoutExclusivity(1);
        $property->setProjet('VH');
        $propertyRepository->add($property);

        return $this->redirectToRoute('op_gestapp_property_show', [
            'id' => $property->getId()
        ]);
    }

    #[Route('/property/editimage/{id}', name: 'op_gestapp_property_editimage', methods: ['POST','GET'])]
    public function editImage(Property $property, Request $request, PropertyRepository $propertyRepository)
    {
        $form = $this->createForm(PropertyImageType::class, $property, [
            'action' => $this->generateUrl('op_gestapp_property_editimage', ['id'=>$property->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_firstedit', ['id'=>$property->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/editimage.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        $complement = $property->getOptions();
        //dd($complement);

        return $this->render('gestapp/property/show.html.twig', [
            'property' => $property,
            'complement' => $complement->getId(),
            'publication' => $property->getPublication(),
        ]);
    }


    #[Route('/{id}/edit', name: 'op_gestapp_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        $complement = $property->getOptions();
        //dd($complement->getId());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/edit.html.twig', [
            'property' => $property,
            'idProperty' => $property->getId(),
            'complement' => $complement->getId(),
            'publication' => $property->getPublication(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/firstedit', name: 'op_gestapp_property_firstedit', methods: ['GET', 'POST'])]
    public function firstedit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {

        $complement = $property->getOptions();
        //dd($complement->getId());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/property/edit.html.twig', [
            'property' => $property,
            'idProperty' => $property->getId(),
            'complement' => $complement->getId(),
            'publication' => $property->getPublication(),
            'form' => $form,
        ]);
    }


    #[Route('/firststep/{id}', name: 'op_gestapp_property_firststep', methods: ['GET', 'POST'])]
    public function firstStep(Request $request, Property $property, PropertyRepository $propertyRepository)
    {
        //dd($property);
        $form = $this->createForm(PropertyStep1Type::class, $property, [
            'action' => $this->generateUrl('op_gestapp_property_firststep', ['id'=>$property->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$annonce = $form->get('annonce')->getData();
            //dd($annonce);

            $array = array_slice(explode(' ', str_replace(array( "\n", "\r", "\u{A0}","</p><p>", "<br>" ), array( '', '',' ', ' ', '' ), strip_tags($property->getAnnonce()))), 0, 10);

            //dd(implode(" ", $array));
            $annonceSlug = implode(" ", $array);
            //dd($annonceSlug);
            $property->setAnnonceSlug($annonceSlug);
            $propertyRepository->add($property);
            //dd($property);
            return $this->json([
                'code'=> 200,
                'message' => "Les informations générales ont été correctement ajoutées au bien."
            ], 200);
        }
        return $this->render('gestapp/property/Step/firststep.html.twig',[
            'form'=>$form,
            'property'=>$property,
        ]);
    }

    #[Route('/secondstep/{id}', name: 'op_gestapp_property_secondstep', methods: ['GET', 'POST'])]
    public function secondStep(Request $request, Property $property, PropertyRepository $propertyRepository)
    {
        //dd($property);
        $form = $this->createForm(PropertyStep2Type::class, $property, [
            'action' => $this->generateUrl('op_gestapp_property_secondstep',['id'=>$property->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);
        //dd($request->getContent());

        if ($form->isSubmitted() && $form->isValid()) {
            $rentalAnnual = $form->get('commerceRentalAnnual')->getData();
            if($rentalAnnual == 0){
                $commerceAnnualRentGlobal = ($form->get('commerceAnnualRentGlobal')->getData())*12;
                $commerceAnnualChargeRentGlobal = ($form->get('commerceAnnualChargeRentGlobal')->getData())*12;
                $commerceAnnualRentMeter = ($form->get('commerceAnnualRentMeter')->getData())*12;
                $commerceAnnualChargeRentMeter = ($form->get('commerceAnnualChargeRentMeter')->getData())*12;
                //dd($rentalAnnual,$commerceAnnualRentGlobal);
                $property->setCommerceAnnualRentGlobal($commerceAnnualRentGlobal);
                $property->setCommerceAnnualChargeRentGlobal($commerceAnnualChargeRentGlobal);
                $property->setCommerceAnnualRentMeter($commerceAnnualRentMeter);
                $property->setCommerceAnnualChargeRentMeter($commerceAnnualChargeRentMeter);
            }
            $propertyRepository->add($property);
            return $this->json([
                'code'=> 200,
                'message' => "Les informations du bien ont été correctement ajoutées."
            ], 200);

        }
        return $this->renderform('gestapp/property/Step/secondstep.html.twig',[
            'form'=>$form,
            'property'=>$property
        ]);
    }

    #[Route('/addmandat/{id}', name: 'op_gestapp_property_addmandat', methods: ['GET', 'POST'])]
    public function addMandat(Request $request, Property $property, PropertyRepository $propertyRepository, EntityManagerInterface $em)
    {
        $form = $this->createForm(AddMandatType::class, $property, [
            'action' => $this->generateUrl('op_gestapp_property_addmandat',['id'=>$property->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $property->setIsNomandat(0);
            $em->persist($property);
            $em->flush();

            return $this->json([
                'code' => 200,
                'message' => "Le numéro de mandat a été correctement ajouté."
                ], 200);
        }

        return $this->render('gestapp/property/_formaddmandat.html.twig',[
            'form'=>$form,
            'property'=>$property
        ]);
    }

    #[Route('/stepinformationsimg/{id}', name: 'op_gestapp_property_stepinformationsimg', methods: ['GET', 'POST'])]
    public function stepInformationsImag(Request $request, Property $property, PropertyRepository $propertyRepository)
    {

        //dd($request->files->get('file'));
        $property->setImageFile($request->files->get('file'));
        $propertyRepository->add($property);
        //dd($property);

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées."
        ], 200);
    }

    #[Route('/{id}', name: 'op_gestapp_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
            $propertyRepository->remove($property);
        }

        return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/archived/{id}', name: 'op_gestapp_property_archived', methods: ['POST'])]
    public function archived(Request $request, Property $property, PropertyRepository $propertyRepository, PaginatorInterface $paginator): Response
    {
        $property->setIsArchived(1);
        $property->setArchivedAt(new \DateTime('+90 days'));
        //dd($property);
        $propertyRepository->add($property);

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true){
            $data = $propertyRepository->listAllProperties();
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            $data2 = $propertyRepository->listAllPropertiesArchived();
            $propertiesArchived = $paginator->paginate(
                $data2,
                $request->query->getInt('page', 1),
                10
            );
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            $data2 = $propertyRepository->listAllPropertiesArchived();
            $propertiesArchived = $paginator->paginate(
                $data2,
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->json([
            'code'=> 200,
            'message' => "Le bien a été archivé sur le site.",
            'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                'properties' => $properties
            ]),
            'listeArchived' => $this->renderView('gestapp/property/_listarchived.html.twig', [
                'properties' => $propertiesArchived
            ])
        ], 200);
    }

    #[Route('/disarchived/{id}', name: 'op_gestapp_property_disarchived', methods: ['POST'])]
    public function disarchived(Request $request, Property $property, PropertyRepository $propertyRepository, PaginatorInterface $paginator): Response
    {
        $property->setIsArchived(0);
        $property->setArchivedAt(null);
        $propertyRepository->add($property);

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true){
            $data = $propertyRepository->listAllProperties();
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            $data2 = $propertyRepository->listAllPropertiesArchived();
            $propertiesArchived = $paginator->paginate(
                $data2,
                $request->query->getInt('page', 1),
                10
            );
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            $data2 = $propertyRepository->listAllPropertiesArchived();
            $propertiesArchived = $paginator->paginate(
                $data2,
                $request->query->getInt('page', 1),
                10
            );
        }
        return $this->json([
            'code'=> 200,
            'message' => "Le bien a été archivé sur le site.",
            'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                'properties' => $properties
            ]),
            'listeArchived' => $this->renderView('gestapp/property/_listarchived.html.twig', [
                'properties' => $propertiesArchived
            ])
        ], 200);

    }

    #[Route('/increatingdel/{id}', name:'op_gestapp_property_increatingdel', methods: ['POST'] )]
    public function increatingDel(Property $property, PropertyRepository $propertyRepository)
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        // Supression du bien sélectionné
        $propertyRepository->remove($property);
        // Affichage de la vue selon 'Employed' ou 'Admin'
        if($hasAccess == true){
            $properties = $propertyRepository->listAllPropertiesIncreating();
            //dd($properties);
            return $this->json([
                'code'=> 200,
                'message' => "Les informations du bien ont été correctement ajoutées.",
                'liste' => $this->renderView('gestapp/property/_increating.html.twig', [
                    'properties' => $properties
                ])
            ], 200);
        }else{
            $properties = $propertyRepository->listPropertiesByEmployedIncreating($user);
            //dd($properties);
            return $this->json([
                'code'=> 200,
                'message' => "Les informations du bien ont été correctement ajoutées.",
                'liste' => $this->renderView('gestapp/property/_increating.html.twig', [
                    'properties' => $properties
                ])
            ], 200);
        }
    }

    #[Route('/del/{id}', name:'op_gestapp_property_del', methods: ['POST', 'GET'] )]
    public function Del(
        Request $request,
        Property $property,
        PropertyRepository $propertyRepository,
        PhotoRepository $photoRepository,
        CadasterRepository $cadasterRepository,
        PublicationRepository $publicationRepository,
        ComplementRepository $complementRepository,
        PaginatorInterface $paginator)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $publication = $property->getPublication();
        $complement = $property->getOptions();

        // Supression des images liées à la propriété
        $photos = $photoRepository->findBy(['property' => $property]);
        foreach($photos as $photo){
            $photoRepository->remove($photo);
        }

        // supression des zones de cadastres liées à la propriété
        $cadasters = $cadasterRepository->findBy(['property' => $property]);
        foreach($cadasters as $cadaster){
            $cadasterRepository->remove($cadaster);
        }

        // Supression de la propriété
        $nameProperty = $property->getName();                   // pour afficher le nom du bien dans le toaster
        $propertyRepository->remove($property);
        $publicationRepository->remove($publication);
        $complementRepository->remove($complement);

        if($hasAccess == true){
            $data = $propertyRepository->listAllProperties();
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            $data = $propertyRepository->listAllPropertiesArchived();
            $propertiesArchived = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
        }else{
            $data = $propertyRepository->listAllProperties();
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user->getId());
            $propertiesArchived = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->json([
            'code'=> 200,
            'message' => 'Les informations du bien : <br>' .$nameProperty. '<br> ont été correctement supprimé.',
            'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                'properties' => $properties
            ]),
            'listeArchived' => $this->renderView('gestapp/property/_listarchived.html.twig', [
                'properties' => $propertiesArchived
            ])
        ], 200);
    }

    /**
     * Suppression en masse des propriétés sélectionnées par Checkbox
     */
    #[Route('/checkboxesdel', name:'op_gestapp_property_checkboxesdel', methods: ['POST'] )]
    public function CheckBoxesDel(Request $request)
    {
        $array = $request->getContent();
        dd($array);
    }

    /**
     * Liste les 10 derniers biens immobiliers sur la page d'accueil.
     */
    #[Route('/lastproperty', name: 'op_gestapp_properties_lastproperty', methods: ['GET'])]
    public function LastProperty(PropertyRepository $propertyRepository)
    {
        $properties = $propertyRepository->fivelastproperties();

        //dd($properties);

        return $this->renderForm('webapp/page/property/lastproperties.html.twig', [
            'properties' => $properties,
        ]);

    }

    /**
     * Affiche la description conmplete d'un bien sur la page "nos biens"
     */
    #[Route('/oneproperty/{id}', name: 'op_gestapp_properties_oneproperty', methods: ['GET'])]
    public function OneProperty(Property $property, PropertyRepository $propertyRepository, PhotoRepository $photoRepository, EmployedRepository $employedRepository)
    {
        // Element nécessaire au controller
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        $complements = $property->getOptions();
        $equipments = $complements->getPropertyEquipment();
        $options = $complements->getPropertyOtheroption();
        $firstphoto = $photoRepository->FirstPhoto($property->getId());
        $employed = $employedRepository->find($property->getRefEmployed());
        //dd($oneproperty);

        return $this->render('webapp/page/property/oneproperty.html.twig', [
            'property' => $oneproperty,
            'equipments' => $equipments,
            'firstphoto' => $firstphoto,
            'options' => $options,
            'employed' => $employed
        ]);
    }

    #[Route('/addAvenant/{id}', name: 'op_gestapp_properties_addavenant', methods: ['POST'])]
    public function AddAvenant(Property $property, PropertyRepository $propertyRepository, Request $request)
    {
        $form = $this->createForm(PropertyAvenantType::class, $property, [
            'action' => $this->generateUrl('op_gestapp_properties_addavenant',['id'=>$property->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        $numberAvenant = $property->getNumberAvenant();
        if(!$numberAvenant){
            $numberAvenant = 1;
        }else{
            $numberAvenant = ++$numberAvenant;
        }
        //dd($form->isValid());

        if ($form->isSubmitted() && $form->isValid()) {
            $property->setNumberAvenant($numberAvenant);
            $property->setDateAvenant(new \DateTime());
            $propertyRepository->add($property);

            return $this->json([
                'code'=> 200,
                'message' => "Les informations du bien ont été correctement ajoutées."

            ], 200);

        }

        //dd($form->getErrors());

        return $this->renderForm('gestapp/property/Step/PriceAvenant.html.twig', [
            'property' => $property,
            'avenant' => $form
        ]);
    }

    /**
     * Affiche tous les biens immobiliers dans la section adaptée".
     */
    #[Route('/allpropertiessales', name: 'op_gestapp_properties_allpropertysales', methods: ['GET'])]
    public function AllPropertiesSales(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request)
    {

        // Récupération de la page si elle existe
        $page = $request;

        $data = $propertyRepository->AllPropertiesSales();

        //dd($data);

        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            24
        );

        return $this->renderForm('webapp/page/property/allproperties.html.twig', [
            'properties' => $properties,
            'page' => $request->query->getInt('page', 1),
        ]);

    }

    /**
     * Affiche tous les biens immobiliers dans la section adaptée".
     */
    #[Route('/allpropertiesrent', name: 'op_gestapp_properties_allpropertyrent', methods: ['GET'])]
    public function AllPropertiesRent(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request): Response
    {

        // Récupération de la page si elle existe
        $page = $request;

        $data = $propertyRepository->AllPropertiesRent();

        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            24
        );

        return $this->renderForm('webapp/page/property/allproperties.html.twig', [
            'properties' => $properties,
            'page' => $request->query->getInt('page', 1),
        ]);

    }



    /**
     * Mettre en place l'archivage d'un bien selon une date de fin de mandat
     */
    #[Route('/add_dateendmandat/{id}', name: 'op_gestapp_properties_adddateendmandat', methods: ['GET','POST'])]
    public function addDateEndMandat(Property $property, PropertyRepository $propertyRepository, Request $request, PaginatorInterface $paginator)
    {
        $form = $this->createForm(PropertyEndMandatType::class, $property, [
            'action' => $this->generateUrl('op_gestapp_properties_adddateendmandat',['id'=>$property->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $propertyRepository->add($property, true);

            $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
            $user = $this->getUser();

            if($hasAccess == true){
                //$data = $propertyRepository->findAll();
                // dans ce cas, nous listons toutes les propriétés de chaque utilisateurs
                $data = $propertyRepository->listAllProperties();
                //dd($data);
                $properties = $paginator->paginate(
                    $data,
                    $request->query->getInt('page', 1),
                    10
                );
                return $this->json([
                    'code'=> 200,
                    'message' => "L'annulation de fin de mandat est bien prise en compte",
                    'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                        'properties' => $properties,
                        'user' => $user
                    ])
                ], 200);
            }else{
                // dans ce cas, nous listons les propriétés de l'utilisateurs courant
                $data = $propertyRepository->listPropertiesByemployed($user);
                $properties = $paginator->paginate(
                    $data,
                    $request->query->getInt('page', 1),
                    10
                );
                return $this->json([
                    'code'=> 200,
                    'message' => "L'annulation de fin de mandat est bien prise en compte",
                    'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                        'properties' => $properties,
                        'user' => $user
                    ])
                ], 200);
            }
        }
        //dd($form);
        return $this->json([
            'form' => $this->renderForm('gestapp/property/_formdateendmandat.html.twig', [
                'form' => $form,
                'property' => $property
            ])
        ], 200);

    }

    /**
     * Annule l'archivage d'un bien selon une date de fin de mandat
     */
    #[Route('/dis_dateendmandat/{id}', name: 'op_gestapp_properties_disdateendmandat', methods: ['GET','POST'])]
    public function disDateEndMandat(Property $property, PropertyRepository $propertyRepository, Request $request, PaginatorInterface $paginator)
    {
        $property->setDateEndmandat(null);
        $propertyRepository->add($property, true);

        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        if($hasAccess == true){
            //$data = $propertyRepository->findAll();
            // dans ce cas, nous listons toutes les propriétés de chaque utilisateurs
            $data = $propertyRepository->listAllProperties();
            //dd($data);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->json([
                'code'=> 200,
                'message' => "L'annulation de fin de mandat est bien prise en compte",
                'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                    'properties' => $properties,
                    'user' => $user
                ])
            ], 200);
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->json([
                'code'=> 200,
                'message' => "L'annulation de fin de mandat est bien prise en compte",
                'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                    'properties' => $properties,
                    'user' => $user
                ])
            ], 200);
        }
    }

}
