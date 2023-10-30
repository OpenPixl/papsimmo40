<?php

namespace App\Service;

use App\Entity\Gestapp\Property;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PropertyService
{
    // Destination commerciale du bien (Vente particulier, vente commerce, location particulier, vente commerce)
    public function getDestination(Property $property)
    {
        //dd($property);
        $famille = $property->getFamily()->getId();
        $rubric = $property->getRubric()->getId();
        //dd($famille);
        if($famille == 8 ){
            //dd('vente immobilier');
            $price = $property->getPrice();
            $priceFai = $property->getPriceFai();
            $rent = "";
            $rentCharge = "";
            $rentWithCharge = "";
            $rentChargeModsPayment = "";
            $warrantyDeposit = "";
            $rentChargeHonoraire = "";
            $commerceAnnualRentGlobal = "";
            $commerceAnnualChargeRentGlobal = "";
            $commerceAnnualRentMeter = "";
            $commerceAnnualChargeRentMeter = "";
            $commerceChargeRentMonthHt = "";
            $commerceRentAnnualCc = "";
            $commerceRentAnnualHt = "";
            $commerceChargeRentAnnualHt = "";
            $commerceRentAnnualMeterCc = "";
            $commerceRentAnnualMeterHt = "";
            $commerceChargeRentAnnualMeterHt = "";
            $commerceSurfaceDivisible = "";
            $commerceSurfaceDivisibleMin = "";
            $commerceSurfaceDivisibleMax = "";
        }elseif($famille == 5){
            //dd('location immobilier');
            $price = "";
            $priceFai = "";
            $rent = $property->getRent();
            $rentCharge = $property->getRentCharge();
            $rentWithCharge = $rent + $rentCharge;
            $warrantyDeposit = $property->getWarrantyDeposit();
            $rentChargeModsPayment = $property->getRentChargeModsPayment();
            $rentChargeHonoraire = $property->getRentChargeHonoraire();
            $commerceAnnualRentGlobal = "";
            $commerceAnnualChargeRentGlobal = "";
            $commerceAnnualRentMeter = "";
            $commerceAnnualChargeRentMeter = "";
            $commerceChargeRentMonthHt = "";
            $commerceRentAnnualCc = "";
            $commerceRentAnnualHt = "";
            $commerceChargeRentAnnualHt = "";
            $commerceRentAnnualMeterCc = "";
            $commerceRentAnnualMeterHt = "";
            $commerceChargeRentAnnualMeterHt = "";
            $commerceSurfaceDivisible = "";
            $commerceSurfaceDivisibleMin = "";
            $commerceSurfaceDivisibleMax = "";
        }elseif($famille == 4 && $rubric == 8){
            //dd('location pro');
            $price = "";
            $priceFai = "";
            $rent = "";
            $rentCharge = "";
            $rentWithCharge = "";
            $rentChargeModsPayment = "";
            $warrantyDeposit = "";
            $rentChargeHonoraire = "";
            $commerceAnnualRentGlobal = $property->getCommerceAnnualRentGlobal();
            $commerceAnnualChargeRentGlobal = $property->getCommerceAnnualChargeRentGlobal();
            $commerceAnnualRentMeter = $property->getCommerceAnnualRentMeter();
            $commerceAnnualChargeRentMeter = $property->getCommerceAnnualChargeRentMeter();
            if($property->IsCommerceChargeRentMonthHt() == 0){
                $commerceChargeRentMonthHt = 'non';
            }else{
                $commerceChargeRentMonthHt = 'oui';
            }
            if($property->IsCommerceRentAnnualCc() == 0){
                $commerceRentAnnualCc = 'non';
            }else{
                $commerceRentAnnualCc = 'oui';
            }
            if($property->IsCommerceRentAnnualHt() == 0){
                $commerceRentAnnualHt = 'non';
            }else{
                $commerceRentAnnualHt = 'oui';
            }
            if($property->IsCommerceChargeRentAnnualHt() == 0){
                $commerceChargeRentAnnualHt = 'non';
            }else{
                $commerceChargeRentAnnualHt = 'oui';
            }
            if($property->IsCommerceRentAnnualMeterCc() == 0){
                $commerceRentAnnualMeterCc = 'non';
            }else{
                $commerceRentAnnualMeterCc = 'oui';
            }
            if($property->IsCommerceRentAnnualMeterHt() == 0){
                $commerceRentAnnualMeterHt = 'non';
            }else{
                $commerceRentAnnualMeterHt = 'oui';
            }
            if($property->IsCommerceChargeRentAnnualMeterHt() == 0){
                $commerceChargeRentAnnualMeterHt = 'non';
            }else{
                $commerceChargeRentAnnualMeterHt = 'oui';
            }
            if($property->IsCommerceSurfaceDivisible() == 0){
                $commerceSurfaceDivisible = 'non';
            }else{
                $commerceSurfaceDivisible = 'oui';
            }
            $commerceSurfaceDivisibleMin = $property->getCommerceSurfaceDivisibleMin();
            $commerceSurfaceDivisibleMax = $property->getCommerceSurfaceDivisibleMax();
        }
        return array(
            'price' => $price, 'priceFai' => $priceFai,
            'rent' => $rent, 'rentCharge' => $rentCharge, 'rentWithCharge' => $rentWithCharge, 'rentChargeModsPayment' => $rentChargeModsPayment, 'rentChargeHonoraire' => $rentChargeHonoraire,
            'warrantyDeposit' => $warrantyDeposit,
            'commerceAnnualRentGlobal' => $commerceAnnualRentGlobal, 'commerceAnnualChargeRentGlobal' => $commerceAnnualChargeRentGlobal, 'commerceAnnualRentMeter' => $commerceAnnualRentMeter,
            'commerceAnnualChargeRentMeter' => $commerceAnnualChargeRentMeter, 'commerceChargeRentMonthHt' => $commerceChargeRentMonthHt, 'commerceRentAnnualCc' => $commerceRentAnnualCc,
            'commerceRentAnnualHt'=> $commerceRentAnnualHt, 'commerceChargeRentAnnualHt' => $commerceChargeRentAnnualHt, 'commerceRentAnnualMeterCc' => $commerceRentAnnualMeterCc,
            'commerceRentAnnualMeterHt' => $commerceRentAnnualMeterHt, 'commerceChargeRentAnnualMeterHt'=>$commerceChargeRentAnnualMeterHt, 'commerceSurfaceDivisible'=>$commerceSurfaceDivisible,
            'commerceSurfaceDivisibleMin'=>$commerceSurfaceDivisibleMin, 'commerceSurfaceDivisibleMax'=>$commerceSurfaceDivisibleMax
        );
    }

    // Génération des références pour les diffuseurs
    public function getRefs(Property $property)
    {
        $dup = $property->getDupMandat();
        if ($dup) {
            $refProperty = $property->getRef() . $dup;
            $refMandat = $property->getRefMandat() . $dup;
        } else {
            $refProperty = $property->getRef();
            $refMandat = $property->getRefMandat();
        }
        return array($refMandat, $refProperty);
    }

    // Détermination des classes des diagnostique dpe et ges
    public function getClasseDpe(Property $property){
        if($property->getDiagChoice() == "obligatoire"){
            // Bilan GES
            if($property->getDiagDpe() > 0 and $property->getDiagDpe() <= 50 ){
                $bilanDpe = 'A';
            }elseif($property->getDiagDpe() > 50 and $property->getDiagDpe() <= 90 ){
                $bilanDpe = 'B';
            }elseif($property->getDiagDpe() > 90 and $property->getDiagDpe()<= 150 ){
                $bilanDpe = 'C';
            }elseif($property->getDiagDpe() > 150 and $property->getDiagDpe() <= 230 ){
                $bilanDpe = 'D';
            }elseif($property->getDiagDpe() > 230 and $property->getDiagDpe() <= 330 ){
                $bilanDpe = 'E';
            }elseif($property->getDiagDpe() > 330 and $property->getDiagDpe() <= 450 ){
                $bilanDpe = 'F';
            }else{
                $bilanDpe = 'G';
            }
        }elseif($property->getDiagChoice() == "vierge"){
            $bilanDpe = "VI";
        }else{
            $bilanDpe = "NS";
        }
        return $bilanDpe;
    }

    public function getClasseGes(Property $property){
        if($property->getDiagChoice() == "obligatoire"){
            // Bilan GES
            if($property->getDiagGes() > 0 and $property->getDiagGes() <= 50 ){
                $bilanGes = 'A';
            }elseif($property->getDiagGes() > 50 and $property->getDiagGes() <= 90 ){
                $bilanGes = 'B';
            }elseif($property->getDiagGes() > 90 and $property->getDiagGes() <= 150 ){
                $bilanGes = 'C';
            }elseif($property->getDiagGes() > 150 and $property->getDiagGes() <= 230 ){
                $bilanGes = 'D';
            }elseif($property->getDiagGes() > 230 and $property->getDiagGes() <= 330 ){
                $bilanGes = 'E';
            }elseif($property->getDiagGes() > 330 and $property->getDiagGes() <= 450 ){
                $bilanGes = 'F';
            }else{
                $bilanGes = 'G';
            }
        }elseif($property['diagChoice'] == "vierge"){
            $bilanGes = "VI";
        }else{
            $bilanGes = "NS";
        }
        return $bilanGes;
    }
}