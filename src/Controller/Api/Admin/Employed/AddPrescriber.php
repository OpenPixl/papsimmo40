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
    public function __invoke(Employed $data, UserPasswordHasherInterface $passwordAuthenticatedUser)
    {
        //dd($data);
        if($data){
            $plainpassword = $data->getPassword();
            $numCollaborator = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
            $referent = $data->getReferent();
            $data->setRoles(['ROLE_PRESCRIBER']);
            $data->setPassword($passwordAuthenticatedUser->hashPassword($data,$plainpassword));
            //dd($numCollaborator);
            $data->setNumCollaborator($numCollaborator);
            $data->setReferent($referent);
            return $data;
        }
        return new JsonResponse(['ko' => 'pas de token']);
    }
}