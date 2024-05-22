<?php

namespace App\Service;


use App\Entity\Gestapp\Property;
use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\CadasterRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\PublicationRepository;
use App\Repository\Gestapp\TransactionRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ArchivePropertyService{

    public function __construct(
        public PropertyRepository $propertyRepository
    )
    {}

    /**
     * Mets en archive les biens possédant une date de fin de mandat
     */
    public function onArchive(PropertyRepository $propertyRepository)
    {
        $now = new \DateTime('now');
        $properties = $propertyRepository->findAll();

        foreach ($properties as $property){
            if($property->getDateEndmandat() == $now){
                $property->setIsArchived(1);
                $property->setArchivedAt(new \DateTime('now'));
                $propertyRepository->add($property, true);
            }
        }
    }

    /**
     * Supprime les biens archivés au bout de trois mois en archive
     */
    public function DelArchived(
        Property $property,
        PhotoRepository $photoRepository,
        CadasterRepository $cadasterRepository,
        PublicationRepository $publicationRepository,
        ComplementRepository $complementRepository,
        TransactionRepository $transactionRepository
    )
    {
        // Suppression des entités liées à la propriété
        $publication = $property->getPublication();
        $complement = $property->getOptions();
        // 1.Supression des images liées à la propriété
        $photos = $photoRepository->findBy(['property' => $property]);
        foreach($photos as $photo){
            $photoRepository->remove($photo);
        }
        // 2.supression des zones de cadastres liées à la propriété
        $cadasters = $cadasterRepository->findBy(['property' => $property]);
        foreach($cadasters as $cadaster){
            $cadasterRepository->remove($cadaster);
        }
        $transactions = $transactionRepository->findBy(['property' => $property]);
        if($transactions){
            foreach($transactions as $t){
                $property->removeTransaction($t);
            }
        }
        // 3. Finalisation des suppression
        $this->propertyRepository->remove($property);
        $publicationRepository->remove($publication);
        $complementRepository->remove($complement);
        }
}