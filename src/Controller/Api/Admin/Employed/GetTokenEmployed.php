<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

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
        return new JsonResponse(['Error' => 'Pas de mandataire avec ce code'], 401);
    }
}