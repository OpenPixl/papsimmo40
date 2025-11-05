<?php

namespace App\Service;

use App\Repository\Admin\ApplicationRepository;

class ApplicationService{

    public function __construct(
        public ApplicationRepository $applicationRepository,
    ){}

    public function getWebappUrl(){
        $application = $this->applicationRepository->findOneBy([], ['id' => 'DESC']);
        $urlWebapp = $application->getUrlAppli();
        return $urlWebapp;
    }

    public function getWebappHost(){
        $application = $this->applicationRepository->findOneBy([], ['id' => 'DESC']);
        $urlWebhost = $application->getHostAppli();
        return $urlWebhost;
    }

    public function getPwaUrl(){
        $application = $this->applicationRepository->findOneBy([], ['id' => 'DESC']);
        $urlPwaapp = $application->getUrlAppli();
        return $urlPwaapp;
    }

    public function getPwaHost(){
        $application = $this->applicationRepository->findOneBy([], ['id' => 'DESC']);
        $urlPwahost = $application->getHostAppli();
        return $urlPwahost;
    }

}