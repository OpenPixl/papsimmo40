<?php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetEmployedByNumCollaborator extends AbstractController
{

    public function __invoke(Employed $data)
    {
        //dd($data);
        if($data){

        }
        return new JsonResponse(['Error' => 'Pas de mandataire avec ce code'], 401);
    }
}