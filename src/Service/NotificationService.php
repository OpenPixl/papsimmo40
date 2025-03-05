<?php

namespace App\Service;

use App\Entity\Admin\Notification;
use App\Repository\Gestapp\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NotificationService
{
    public function __construct(
        public EntityManagerInterface $em,
        public PropertyRepository $propertyRepository,
        protected RequestStack $request
    )
    {}

    public function setDocumentTransaction($support, $service, $data){
        $employed = $data->getRefEmployed();
        $log = [
            'Support' => $support,
            'Service' => $service,
            'Step' => 'Insertion date "Acte de vente"',
            'infos' => $data->getName(),
            'Etat' => 200,
        ];
        $request = Request::createFromGlobals();
        $notification = new Notification();
        $notification->setRefEmployed($employed);
        $notification->setIsApi(0);
        $notification->setLog($log);
        $notification->setClientHost($request->getClientIp());
        $notification->setSession($this->request->getSession()->getName());

        $this->em->persist($notification);
        $this->em->flush();
    }
}