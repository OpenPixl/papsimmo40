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
        if(is_null($transaction->getInvoicePdfFilename())){$isInvoice = 0;}else{$isInvoice = 1;}
        //dd($isDatePromise,$isDateSale,$isPromise,$isPromiseValid,$isActe,$isActeValid,$isTracfin,$isTracfinValid,$isHonoraires,$isInvoice);
        array_push($projectArray, $isDatePromise,$isDateSale,$isPromise,$isPromiseValid,$isActe,$isActeValid,$isTracfin,$isTracfinValid,$isHonoraires,$isInvoice);
        $project = (array_sum($projectArray)/count($projectArray))*100;

        return $project;
    }
}