<?php

namespace App\Service\Transfert;

use App\Entity\Gestapp\Photo;
use App\Entity\Gestapp\Property;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Service\PropertyService;
use Symfony\Component\String\Slugger\SluggerInterface;

class TransfertPhotos
{
    public function __construct(
        public ApplicationRepository $applicationRepository,
        public PropertyRepository $propertyRepository,
        public PhotoRepository $photoRepository,
        public SluggerInterface $slugger,
        public PropertyService $propertyService,
    ){}

    public function getRefs($property){
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        return $ref;
    }

    public function getName($property){
        $application = $this->applicationRepository->find(1);
        $nameApp = preg_replace('/\s+/', '', $application->getNameSite());
        $nameApp = $this->slugger->slug($nameApp)->lower();

        return $nameApp;
    }
}