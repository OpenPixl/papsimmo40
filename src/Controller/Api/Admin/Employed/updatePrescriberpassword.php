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
class updatePrescriberpassword extends AbstractController
{
    public function __invoke(Employed $data, Request $request, UserPasswordHasherInterface $passwordAuthenticatedUser, EntityManagerInterface $em)
    {
        //dd($data);
        if($data){
            $user = $this->getUser();
            $plainpassword = $data->getPassword();
            $key = $passwordAuthenticatedUser->hashPassword($data,$plainpassword);
            $data->setPassword($key);

            $log = array($data);
            $notification = new Notification();
            $notification->setRefEmployed($user);
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