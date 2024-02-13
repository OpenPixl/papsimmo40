<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use App\Entity\Admin\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class AddPrescriber extends AbstractController
{
    public function __invoke(Employed $data, Request $request, UserPasswordHasherInterface $passwordAuthenticatedUser, EntityManagerInterface $em)
    {
        //dd($data);
        if($data){
            $plainpassword = $data->getPassword();
            $numCollaborator = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
            $referent = $data->getReferent();
            $data->setRoles(['ROLE_PRESCRIBER']);
            $data->setPassword($passwordAuthenticatedUser->hashPassword($data,$plainpassword));
            $data->setNumCollaborator($numCollaborator);
            $data->setReferent($referent);

            $log = array($data);
            $notification = new Notification();
            $notification->setRefEmployed($data->getReferent());
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