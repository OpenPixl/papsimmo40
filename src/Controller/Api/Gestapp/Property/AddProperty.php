<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Gestapp\Property;

use App\Entity\Gestapp\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AddProperty extends AbstractController
{

    public function __invoke(Property $data)
    {
        //dd($data);

    }
}