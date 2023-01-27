<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\PropertyDefinition;
use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\PropertyAvenantType;
use App\Form\Gestapp\PropertyImageType;
use App\Form\Gestapp\PropertyStep1Type;
use App\Form\Gestapp\PropertyStep2Type;
use App\Form\Gestapp\PropertyType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\CadasterRepository;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use App\Repository\Gestapp\choice\PropertyDefinitionRepository;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Webapp\choice\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/gestapp/property')]
class PropertyController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request  $request): Response
    {
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
            return $this->render('gestapp/property/index.html.twig', [
                'properties' => $properties,
                'user' => $user
            ]);
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
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
                'properties' => $propertyRepository->findBy(array('isIncreating' => 1)),
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
            $propertyRepository->add($property);
            return $this->redirectToRoute('op_gestapp_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/add', name:'op_gestapp_property_add', methods: ['GET', 'POST'])]
    public function add(
        PropertyRepository $propertyRepository,
        EmployedRepository $employedRepository,
        ComplementRepository $complementRepository,
        PublicationRepository $publicationRepository,
        PropertyEquipementRepository $propertyEquipementRepository,
        OtherOptionRepository $otherOptionRepository,
        PropertyDefinitionRepository $propertyDefinitionRepository
        )
    {
        // Récupération du collaborateur
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        // prépartion des complement au bien
        $complement = new Complement();
        $complement->setTerrace(0);
        $complement->setWashroom(0);
        $complement->setBathroom(0);
        $complement->setWc(0);
        $complement->setBalcony(0);
        $complement->setPropertyTax(0);
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
        $lastproperty = $propertyRepository->findOneBy([], ['id'=>'desc']);           // Récupération de la dernière propriété enregistrée
        if($lastproperty){
            $refNumDate = $date->format('Y').'/'.$date->format('m');        // contruction de la première partie de référence
            $RefMandat = $lastproperty->getRefMandat() + 1;                           // construction du numéro de mandat obligatoire
        }else{
            $refNumDate = $date->format('Y').'/'.$date->format('m');        // contruction de la première partie de référence
            $RefMandat = 23;
        }

        // Création de l'entité Property
        $property = new Property();
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

        return $this->renderForm('gestapp/property/edit.html.twig', [
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
        //dd($request->getContent());

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $propertyRepository->add($property);
            return $this->json([
                'code'=> 200,
                'message' => "Les informations générales ont été correctement ajoutées au bien."
            ], 200);

        }
        //dd($form->isSubmitted());
        return $this->renderform('gestapp/property/Step/firststep.html.twig',[
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
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            $properties = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->json([
            'code'=> 200,
            'message' => "Le bien a été archivé sur le site.",
            'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                'properties' => $properties
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

    #[Route('/del/{id}', name:'op_gestapp_property_del', methods: ['POST'] )]
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
        }else{
            // dans ce cas, nous listons les propriétés de l'utilisateurs courant
            $data = $propertyRepository->listPropertiesByemployed($user);
            $properties = $paginator->paginate(
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
     * Liste les 5 derniers biens immobiliers sur la page d'accueil.
     */
    #[Route('/lastproperty', name: 'op_gestapp_properties_lastproperty', methods: ['GET'])]
    public function LastProperty(PropertyRepository $propertyRepository)
    {
        $properties = $propertyRepository->fivelastproperties();

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

        //dd($form->isValid());

        if ($form->isSubmitted() && $form->isValid()) {
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
     * Affiche tous les biens immobiliers sur la page "Nos biens".
     */
    #[Route('/allproperties', name: 'op_gestapp_properties_allproperty', methods: ['GET'])]
    public function AllProperties(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request)
    {

        $data = $propertyRepository->listAllProperties();

        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            12
        );

        return $this->renderForm('webapp/page/property/allproperties.html.twig', [
            'properties' => $properties,
        ]);

    }

}
