<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Complement;
use App\Form\Gestapp\ComplementType;
use App\Repository\Gestapp\choice\ApartmentTypeRepository;
use App\Repository\Gestapp\choice\BuildingEquipmentRepository;
use App\Repository\Gestapp\choice\DenominationRepository;
use App\Repository\Gestapp\choice\HouseEquipmentRepository;
use App\Repository\Gestapp\choice\HouseTypeRepository;
use App\Repository\Gestapp\choice\LandTypeRepository;
use App\Repository\Gestapp\choice\OtherOptionRepository;
use App\Repository\Gestapp\choice\TradeTypeRepository;
use App\Repository\Gestapp\ComplementRepository;
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
            return $this->redirectToRoute('op_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
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
        HouseTypeRepository $houseTypeRepository,
        ApartmentTypeRepository $apartmentTypeRepository,
        LandTypeRepository $landTypeRepository,
        TradeTypeRepository $tradeTypeRepository,
        HouseEquipmentRepository $houseEquipmentRepository,
        BuildingEquipmentRepository $buildingEquipmentRepository,
        OtherOptionRepository $otherOptionRepository,
    )
    {
        //Récupération des variables das le data d'Axios
        $data = json_decode($request->getContent(), true);

        $idDenomination = $data['denomination'];
        $denomination = $denominationRepository->find($idDenomination);

        $disponibilityAt = new \DateTime($data['disponibilityAt']);
        $constructionAt = new \DateTime($data['constructionAt']);

        $idhouseType = $data['houseType'];
        $houseType = $houseTypeRepository->find($idhouseType);

        $idapartmentType = $data['apartmentType'];
        $apartmentType = $apartmentTypeRepository->find($idapartmentType);

        $idlandType = $data['landType'];
        $landType = $landTypeRepository->find($idlandType);

        $idtradeType = $data['tradeType'];
        $tradeType = $tradeTypeRepository->find($idtradeType);

        $idhouseEquipment = $data['houseEquipment'];
        $houseEquipment = $houseEquipmentRepository->find($idhouseEquipment);

        $idbuildingEquipment = $data['buildingEquipment'];
        $buildingEquipment = $buildingEquipmentRepository->find($idbuildingEquipment);

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
        $complement->setSanitation($data['sanitation']);
        $complement->setJointness($data['jointness']);
        $complement->setHouseState($data['houseState']);
        $complement->setEnergy($data['energy']);
        $complement->setPropertyTax($data['propertyTax']);
        $complement->setOrientation($data['orientation']);
        $complement->setDisponibility($data['disponibility']);
        $complement->setLocation($data['location']);
        $complement->setDisponibilityAt($disponibilityAt);
        $complement->setConstructionAt($constructionAt);
        $complement->setIsFurnished($data['isFurnished']);
        $complement->setHouseType($houseType);
        $complement->setApartmentType($apartmentType);
        $complement->setLandType($landType);
        $complement->setTradeType($tradeType);
        $complement->setHouseEquipment($houseEquipment);
        $complement->setLevel($data['level']);
        $complement->setBuildingEquipment($buildingEquipment);
        $complement->setOtherOption($otherOption);

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
    public function edit(Request $request, Complement $complement, ComplementRepository $complementRepository): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complementRepository->add($complement);
            return $this->redirectToRoute('op_gestapp_complement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/complement/edit.html.twig', [
            'complement' => $complement,
            'form' => $form,
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
