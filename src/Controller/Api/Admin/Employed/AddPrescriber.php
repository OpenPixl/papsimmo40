<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class AddPrescriber extends AbstractController
{

    public function __invoke(Employed $data, UserPasswordHasherInterface $userPasswordHasher)
    {
        //dd($data);
        if($data){
            $data->setRoles(['ROLE_PRESCRIBER']);
            $data->setPassword($userPasswordHasher->hashPassword($data,$data->getPassword()));
            return $data;
        }
        return new JsonResponse(['ko' => 'pas de token']);
    }
}