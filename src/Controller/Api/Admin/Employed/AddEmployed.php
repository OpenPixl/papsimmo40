<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AddEmployed extends AbstractController
{

    public function __invoke(Employed $data)
    {
        //dd($data);
        if($data){
            $token =  new JsonResponse(['token' => $JWTManager->create($data)]);
            return $token;
        }
        return new JsonResponse(['ko' => 'pas de token']);
    }
}