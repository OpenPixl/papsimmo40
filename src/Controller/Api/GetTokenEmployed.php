<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api;

use App\Entity\Admin\Employed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[AsController]
class GetTokenEmployed extends AbstractController
{

    public function __invoke(Employed $data, JWTTokenManagerInterface $JWTManager)
    {
        //dd($data);
        if($data){
            $token =  new JsonResponse(['token' => $JWTManager->create($data)]);
            return $token;
        }
        return new JsonResponse(['ko' => 'pas de token']);
    }
}