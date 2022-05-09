<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Publication;
use App\Form\Gestapp\PropertyType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/property')]
class PropertyController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('gestapp/property/index.html.twig', [
            'properties' => $propertyRepository->findBy(array('isIncreating' => 0)),
        ]);
    }

    #[Route('/inCreating', name: 'op_gestapp_property_inCreating', methods: ['GET'])]
    public function inCreating(PropertyRepository $propertyRepository): Response
    {
        return $this->render('gestapp/property/increating.html.twig', [
            'properties' => $propertyRepository->findBy(array('isIncreating' => 1)),
        ]);
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
        PublicationRepository $publicationRepository)
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $complement = new Complement();
        $complementRepository->add($complement);

        $publication = new Publication();
        $publicationRepository->add($publication);

        $date = new \DateTime();

        $property = new Property();
        $property->setName('Nouveau bien');
        $property->setRef('PAPS-'. $date->format('Y').$date->format('m'));
        $property->setRefEmployed($employed);
        $property->setOptions($complement);
        $property->setPublication($publication);
        $property->setIsIncreating(1);
        $propertyRepository->add($property);

        return $this->redirectToRoute('op_gestapp_property_firstedit', [
            'id' => $property->getId()
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        return $this->render('gestapp/property/show.html.twig', [
            'property' => $property,
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

    #[Route('/stepinformations/{id}', name: 'op_gestapp_property_stepinformations', methods: ['GET', 'POST'])]
    public function stepInformations(Request $request, Property $property, PropertyRepository $propertyRepository)
    {
        //dd($property);
        $data = json_decode($request->getContent(), true);

        $property->setName($data['name']);
        $property->setRef($data['ref']);
        $property->setAdress($data['adress']);
        $property->setComplement($data['complement']);
        $property->setZipcode($data['zipcode']);
        $property->setCity($data['city']);
        $property->setAnnonce($data['annonce']);
        $property->setPiece($data['piece']);
        $property->setRoom($data['room']);
        $property->setIsHome($data['isHome']);
        $property->setIsApartment($data['isApartment']);
        $property->setIsLand($data['isLand']);
        $property->setIsOther($data['isOther']);
        $property->setOtherDescription($data['otherDescription']);

        $propertyRepository->add($property);

        //dd($property);

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées."
        ], 200);
    }

    #[Route('/stepchiffres/{id}', name: 'op_gestapp_property_stepchiffres', methods: ['GET', 'POST'])]
    public function stepChiffres(Request $request, Property $property, PropertyRepository $propertyRepository)
    {
        //dd($property);
        $data = json_decode($request->getContent(), true);

        $dpeAt = new \DateTime($data['dpeAt']);

        $property->setSurfaceLand($data['surfaceLand']);
        $property->setSurfaceHome($data['surfaceHome']);
        $property->setNotaryEstimate($data['notaryEstimate']);
        $property->setApplicantEstimate($data['applicantEstimate']);
        $property->setDpeAt($dpeAt);
        $property->setDiagDpe($data['diagDpe']);
        $property->setDiagGpe($data['diagGpe']);
        $property->setCadasterZone($data['cadasterZone']);
        $property->setCadasterNum($data['cadasterNum']);
        $property->setCadasterSurface($data['cadastersurface']);
        $property->setCadasterCariez($data['cadasterCariez']);

        $propertyRepository->add($property);

        //dd($property);

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées."
        ], 200);
    }

    #[Route('/steppublication/{id}', name: 'op_gestapp_property_steppublication', methods: ['GET', 'POST'])]
    public function stepPublication(
        Request $request,
        Property $property,
        PropertyRepository $propertyRepository,
        PublicationRepository $publicationRepository
    )
    {
        // récupération de l'objet Publication correspodant à la Propriété
        $idpublication = $property->getPublication();
        $publication = $publicationRepository->find($idpublication);
        // Extraction des datas d'Axios
        $data = json_decode($request->getContent(), true);
        // hydratation de l'objet Publication
        $publication->setIsSocialNetwork($data['isWebpublish']);
        $publication->setIsWebpublish($data['isSocialNetwork']);
        $publication->setSector($data['sector']);
        // Flush de l'objet Publication
        $publicationRepository->add($publication);
        // Finalisation des étapes de Créations de lma propriété et Flush
        $property->setIsIncreating(0);
        $propertyRepository->add($property);

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

    #[Route('/increatingdel/{id}', name:'op_gestapp_property_increatingdel', methods: ['POST'] )]
    public function increatingDel(Property $property, PropertyRepository $propertyRepository)
    {
        $propertyRepository->remove($property);

        $properties = $propertyRepository->findBy(array('isIncreating' => 1));

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées.",
            'liste' => $this->renderView('gestapp/property/_increating.html.twig', [
                'properties' => $properties
                ])
        ], 200);
    }

    #[Route('/del/{id}', name:'op_gestapp_property_del', methods: ['POST'] )]
    public function Del(Property $property, PropertyRepository $propertyRepository)
    {
        $propertyRepository->remove($property);

        $properties = $propertyRepository->findBy(array('isIncreating' => 0));

        return $this->json([
            'code'=> 200,
            'message' => "Les informations du bien ont été correctement ajoutées.",
            'liste' => $this->renderView('gestapp/property/_list.html.twig', [
                'properties' => $properties
            ])
        ], 200);
    }

}
