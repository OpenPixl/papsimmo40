<?php
// api/src/Controller/CreateBookPublication.php
namespace App\Controller\Api\Gestapp\Prescriber;


use App\Entity\Admin\Prescriber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class addPrescriber extends AbstractController
{
    public function __invoke(Prescriber $data, UserPasswordHasherInterface $userPasswordHasher)
    {
        $data->setPassword($userPasswordHasher->hashPassword($data,$data->getPassword()));
        return $data;
    }
}