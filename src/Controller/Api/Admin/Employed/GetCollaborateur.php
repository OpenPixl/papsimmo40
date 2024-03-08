<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Admin\Employed;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetCollaborateur extends AbstractController
{

    public function __invoke(EmployedRepository $employedRepository)
    {
        //dd($data);
        $data = $employedRepository->listPrescriber('ROLE_EMPLOYED');

        return new JsonResponse(['Error' => 'Pas de collaborateur'], 401);
    }
}