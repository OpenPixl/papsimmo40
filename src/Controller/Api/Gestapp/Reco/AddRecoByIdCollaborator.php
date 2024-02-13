<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Gestapp\Reco;

use App\Entity\Gestapp\Reco;
use App\Entity\Admin\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class AddRecoByIdCollaborator extends AbstractController
{

    public function __invoke(Reco $data, EntityManagerInterface $em)
    {
        //dd($data);
        if($data){
            $employed = $data->getRefEmployed();
            $log = array($data);
            $request = Request::createFromGlobals();
            $notification = new Notification();
            $notification->setRefEmployed($employed);
            $notification->setIsApi(1);
            $notification->setLog($log);
            $notification->setClientHost($request->getClientIp());

            $em->persist($notification);
            $em->flush();

            return $data;
        }
        return new JsonResponse(['ko' => 'pas de token']);
    }
}