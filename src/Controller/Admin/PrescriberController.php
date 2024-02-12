<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PrescriberController extends AbstractController
{
    #[Route('/admin/prescriber/', name: 'op_admin_prescriber_index', methods: ['GET'])]
    public function index(EmployedRepository $employedRepository): Response
    {
        $prescribers = $employedRepository->listPrescriber('["ROLE_PRESCRIBER"]');

        return $this->render('admin/employed/prescriber.html.twig', [
            'prescribers' => $prescribers,
        ]);
    }
}