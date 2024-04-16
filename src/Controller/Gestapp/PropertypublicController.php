<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Photo;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/propertypublic')]
class PropertypublicController extends AbstractController
{
    /**
     * Liste les 10 derniers biens immobiliers sur la page d'accueil.
     */
    #[Route('/lastproperty', name: 'op_gestapp_properties_lastproperty', methods: ['GET'])]
    public function LastProperty(PropertyRepository $propertyRepository)
    {
        $properties = $propertyRepository->fivelastproperties();

        //dd($properties);

        return $this->render('webapp/page/property/lastproperties.html.twig', [
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

    /**
     * Affiche tous les biens immobiliers dans la section adaptée".
     */
    #[Route('/allpropertiessales', name: 'op_gestapp_properties_allpropertysales', methods: ['GET'])]
    public function AllPropertiesSales(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request)
    {
        // Récupération de la page si elle existe
        $page = $request->get('page');//

        //dd($page);

        $data = $propertyRepository->AllPropertiesSales();

        //dd($data);

        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            24
        );

        if(!$page){
            return $this->render('webapp/page/property/allproperties.html.twig', [
                'properties' => $properties,
                'page' => $request->query->getInt('page', 1),
            ]);
        }else{
            return $this->json([
                'liste' => $this->renderView('webapp/page/property/allproperties.html.twig', [
                    'properties' => $properties,
                    'page' => $request->query->getInt('page', $page),
                ])
            ], 200);
        }
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

        return $this->render('webapp/page/property/allproperties.html.twig', [
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
