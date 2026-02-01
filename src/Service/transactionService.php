<?php

namespace App\Service;

use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class transactionService
{
    public function __construct(
        public  EntityManagerInterface $em,
        public PropertyRepository $propertyRepository,
        protected RequestStack $request
    )
    {}

    // Calcule le taux de progression du projet
    public function calculateProject(Transaction $transaction){
        $projectArray = [];

        if(is_null($transaction->getDateAtPromise())){$isDatePromise = 0;}else{$isDatePromise = 1;}
        if(is_null($transaction->getDateAtSale())){$isDateSale = 0;}else{$isDateSale = 1;}
        if(is_null($transaction->getPromisePdfFilename())){$isPromise = 0;}else{$isPromise = 1;}
        if($transaction->isIsValidPromisepdf() == 0){$isPromiseValid = 0;}else{$isPromiseValid = 1;}
        if(is_null($transaction->getActePdfFilename())){$isActe = 0;}else{$isActe = 1;}
        if($transaction->isIsValidActepdf() == 0){$isActeValid = 0;}else{$isActeValid = 1;}
        if(is_null($transaction->getTracfinPdfFilename())){$isTracfin = 0;}else{$isTracfin = 1;}
        if($transaction->isIsValidtracfinPdf() == 0){$isTracfinValid = 0;}else{$isTracfinValid = 1;}
        if(is_null($transaction->getHonorairesPdfFilename())){$isHonoraires = 0;}else{$isHonoraires = 1;}
        if($transaction->isIsValidHonoraires() == 0){$isHonorairesValid = 0;}else{$isHonorairesValid = 1;}
        if(is_null($transaction->getInvoicePdfFilename())){$isInvoice = 0;}else{$isInvoice = 1;}
        if($transaction->isIsValidInvoicePdf() == 0){$isInvoiceValid = 0;}else{$isInvoiceValid = 1;}
        array_push($projectArray, $isDatePromise,$isDateSale,$isPromise,$isPromiseValid,$isActe,$isActeValid,$isTracfin,$isTracfinValid,$isHonoraires,$isInvoice);
        $project = (array_sum($projectArray)/count($projectArray))*100;

        return $project;
    }

    // Validation des étapes de la transaction
    public function step(Transaction $transaction){

        if($transaction->isCancelled() == 0)
        {
            if(!$transaction->getCustomer()->count() > 0) {
                //dd(1);
                $transaction->setState('Promesse de vente | En attente d\'un ou de plusieurs acquéreurs.');
                $transaction->setStep(0);
                $this->em->flush();
                return 0;
            }
            if(!$transaction->getDateAtPromise()){ // Un acheteur est ajoutée au dossier, il faut déposer le pdf de la promesse de vente.
                //dd(0);
                $transaction->setState('Promesse de vente | En attente de la date du RDV');
                $transaction->setStep(1);
                $this->em->flush();
                return 1;
            }
            if(!$transaction->getPromisePdfFilename()){
                //dd(2);
                $transaction->setState('Promesse de vente | En attente du chargement du fichier Pdf');
                $transaction->setStep(2);
                $this->em->flush();
                return 2;
            }
            if(!$transaction->isIsValidPromisepdf()){ // La promesse de vente est déposée mais doit être validée par un Admin
                //dd(3);
                $transaction->setState('Promesse de vente | En attente de la validation du pdf par l\'administrateur');
                $transaction->setStep(3);
                $this->em->flush();
                return 3;
            }
            if(!$transaction->getDateAtSale()){
                //dd(6);
                $transaction->setState('Acte de vente et Tracfin | En attente de la date du RDV');
                $transaction->setStep(4);
                $this->em->flush();
                return 4;
            }
            if(!$transaction->getHonorairesPdfFilename()){
                //dd(4);
                $transaction->setState('Honoraires | En attente du chargement du fichier Pdf par l\'administrateur');
                $transaction->setStep(5);
                $this->em->flush();
                return 5;
            }
            if(!$transaction->getActePdfFilename() && !$transaction->getTracfinPdfFilename()){
                //dd(7);
                $transaction->setState('Acte de vente et Tracfin | En attente du chargement du premier document');
                $transaction->setStep(6);
                $this->em->flush();
                return 6;
            }
            if(($transaction->getActePdfFilename() && !$transaction->getTracfinPdfFilename()) || (!$transaction->getActePdfFilename() && $transaction->getTracfinPdfFilename())){
                //dd(7);
                $transaction->setState('Acte de vente et Tracfin | En attente du chargement du dernier document');
                $transaction->setStep(7);
                $this->em->flush();
                return 7;
            }
            if((!$transaction->isIsValidActepdf() && !$transaction->isIsValidTracfinpdf()) || ($transaction->isIsValidActepdf() && !$transaction->isIsValidTracfinpdf()) || (!$transaction->isIsValidActepdf() && $transaction->isIsValidTracfinpdf()) ){
                //dd(8);
                $transaction->setState('Acte de vente et Tracfin | En attente de la validation du pdf par l\'administrateur');
                $transaction->setStep(8);
                $this->em->flush();
                return 8;
            }
            if(!$transaction->getInvoicePdfFilename()){
                //dd(9);
                $transaction->setState('Facture de vente | En attente du chargement du fichier Pdf');
                $transaction->setStep(9);
                $this->em->flush();
                return 9;
            }
            if(!$transaction->isIsValidInvoicePdf()){
                //dd(10);
                $transaction->setState("Facture de vente | Validée par l'administrateur");
                $transaction->setStep(10);
                $this->em->flush();
                return 10;
            }
            if($transaction->isCollaborator() == 1){

                $collaborateurs = $transaction->getAddCollTransacs();
                if ($collaborateurs->count() === 0) {
                    //dd('11A');
                    // Aucun collaborateur, on reste dans l'état actuel ou on log si besoin
                    return $transaction->getStep();
                }

                $totalCollaborateurs = $collaborateurs->count();
                $collaborateursAvecFacture = 0;

                foreach ($collaborateurs as $collab) {
                    if (!empty($collab->getInvoicePdfFilename())) {
                        $collaborateursAvecFacture++;
                    }
                }

                if ($collaborateursAvecFacture === 0) {
                    //dd('11B');
                    // Aucun collaborateur n’a déposé sa facture
                    $transaction->setState('Autres Factures | En attente des pièces');
                    $transaction->setStep(11);
                    $this->em->flush();
                    return 11;
                }

                if ($collaborateursAvecFacture === $totalCollaborateurs) {
                    // Tous les collaborateurs ont fourni leur facture
                    $transaction->setState('Autres Factures | La ou les factures sont déposées');
                    $transaction->setStep(12);
                    $this->em->flush();
                    return 12;
                }

                // Si certains ont mis une facture, mais pas tous : on ne change rien ou on peut log l'état partiel
                return $transaction->getStep();
            }
        }else{
            if(!$transaction->getAnnulation()->getSupportName()){

                $transaction->setState('Annulation de la vente | En attente du document notarié par l\'administrateur.');
                $transaction->setStep(13);
                $this->em->flush();
                return 13;
            }
            if(!$transaction->getAnnulation()->isValidNotarialDoc()){
                $transaction->setState('Annulation de la vente | En attente de la validation du document notarié.');
                $transaction->setStep(14);
                $this->em->flush();
                return 14;
            }
            if(!$transaction->getAnnulation()->getSupportFact()){
                $transaction->setState('Annulation de la vente | En attente de la facture collaborateur.');
                $transaction->setStep(15);
                $this->em->flush();
                return 15;
            }
            if(!$transaction->getAnnulation()->getSupportFactColl()){
                $transaction->setState('Annulation de la vente | En attente de la facture collaborateur.');
                $transaction->setStep(16);
                $this->em->flush();
                return 16;
            }
            if(!$transaction->getAnnulation()->isValidFactAdmin()){
                $transaction->setState('Annulation de la vente | En attente de la facture collaborateur.');
                $transaction->setStep(17);
                $this->em->flush();
                return 17;
            }
        }

        $transaction->setStep(18);
        $transaction->setIsDocsFinished(1);
        $this->em->flush();

        $step = $transaction->getStep();

        return $step;
    }
}