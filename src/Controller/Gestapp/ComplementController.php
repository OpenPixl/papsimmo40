<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\PropertyEquipement;
use App\Entity\Gestapp\Complement;
use App\Form\Gestapp\ComplementType;
use App\Repository\Gestapp\choice\ApartmentTypeRepository;
use App\Repository\Gestapp\choice\BuildingEquipmentRepository;
use App\Repository\Gestapp\choice\DenominationRepository;
use App\Repository\Gestapp\choice\HouseEquipmentRepository;
use App\Repository\Gestapp\choice\HouseTypeRepository;
use App\Repository\Gestapp\choice\LandTypeRepository;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use App\Repository\Gestapp\choice\PropertyEquipementRepository;
use App\Repository\Gestapp\choice\PropertyOrientationRepository;
use App\Repository\Gestapp\choice\PropertyStateRepository;
use App\Repository\Gestapp\choice\PropertyTypologyRepository;
use App\Repository\Gestapp\choice\TradeTypeRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/complement')]
class ComplementController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_complement_index', methods: ['GET'])]
    public function index(ComplementRepository $complementRepository): Response
    {
        return $this->render('gestapp/complement/index.html.twig', [
            'complements' => $complementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ComplementRepository $complementRepository): Response
    {
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complementRepository->add($complement);
            return $this->json([
                'code' => 200,
                'message' => "Une nouvelle source a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/complement/new.html.twig', [
            'complement' => $complement,
            'form' => $form,
        ]);
    }

    #[Route('/addcomplemet/{id}', name: 'op_gestapp_customer_addcomplement',  methods: ['GET', 'POST'])]
    public function addComplement(
        Request $request,
        Complement $complement,
        ComplementRepository $complementRepository,
        DenominationRepository $denominationRepository,
        OtherOptionRepository $otherOptionRepository,
        PropertyEquipementRepository $propertyEquipementRepository,
        PropertyStateRepository $propertyStateRepository,
        PropertyEnergyRepository $propertyEnergyRepository,
        PropertyTypologyRepository $propertyTypologyRepository,
        PropertyOrientationRepository $propertyOrientationRepository
    )
    {
        //Récupération des variables das le data d'Axios
        $data = json_decode($request->getContent(), true);
        //dd($data);

        $idDenomination = $data['denomination'];
        $denomination = $denominationRepository->find($idDenomination);

        $disponibilityAt = new \DateTime($data['disponibilityAt']);

        $idpropertyState = $data['propertyState'];
        $propertyState = $propertyStateRepository->find($idpropertyState);

        $idpropertyEnergy = $data['propertyEnergy'];
        $propertyEnergy = $propertyEnergyRepository->find($idpropertyEnergy);

        $idpropertyOrientation = $data['propertyOrientation'];
        $propertyOrientation = $propertyOrientationRepository->find($idpropertyOrientation);

        $propertiesEquipment = $data['propertyEquipment'];

        $idpropertyTypology = $data['propertyTypology'];
        $propertyTypology = $propertyTypologyRepository->find($idpropertyTypology);

        $idotherOption = $data['otherOption'];
        $otherOption = $otherOptionRepository->find($idotherOption);

        // hydratation de l'objet Complement
        $complement->setBanner($data['banner']);
        $complement->setDenomination($denomination);

        $complement->setTerrace($data['terrace']);
        $complement->setWashroom($data['washroom']);
        $complement->setBathroom($data['bathroom']);
        $complement->setWc($data['wc']);
        $complement->setBalcony($data['balcony']);

        $complement->setPropertyState($propertyState);
        $complement->setPropertyEnergy($propertyEnergy);
        $complement->setPropertyTax($data['propertyTax']);
        $complement->setPropertyOrientation($propertyOrientation);
        $complement->setDisponibility($data['disponibility']);
        $complement->setLocation($data['location']);
        $complement->setDisponibilityAt($disponibilityAt);
        $complement->setCoproprietyTaxe(0);

        $complement->setIsFurnished($data['isFurnished']);

        $complement->setPropertyTypology($propertyTypology);
        $complement->setLevel($data['level']);
        $complement->setOtherOption($otherOption);
        foreach ($propertiesEquipment as $equip){
            $equipement = $propertyEquipementRepository->find($equip);
            $complement->addPropertyEquipment($equipement);
        }
        // flush
        $complementRepository->add($complement);
        return $this->json([
            'code'=> 200,
            'message' => "Les options ont été correctement ajoutées."
        ], 200);

    }

    #[Route('/{id}', name: 'op_gestapp_complement_show', methods: ['GET'])]
    public function show(Complement $complement): Response
    {
        return $this->render('gestapp/complement/show.html.twig', [
            'complement' => $complement,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Complement $complement, ComplementRepository $complementRepository, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->findOneBy(['options'=> $complement->getId()]);
        $form = $this->createForm(ComplementType::class, $complement, [
            'action' => $this->generateUrl('op_gestapp_complement_edit', ['id'=>$complement->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complementRepository->add($complement);
            return $this->json([
                'code' => 200,
                'message' => "Une nouvelle source a été ajoutée."
            ], 200);
        }

        return $this->renderForm('gestapp/complement/edit.html.twig', [
            'complement' => $complement,
            'form' => $form,
            'property' => $property
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_complement_delete', methods: ['POST'])]
    public function delete(Request $request, Complement $complement, ComplementRepository $complementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$complement->getId(), $request->request->get('_token'))) {
            $complementRepository->remove($complement);
        }

        return $this->redirectToRoute('op_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
    }
}
