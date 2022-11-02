<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/admin/mail', name: 'app_admin_mail')]
    public function index(): Response
    {
        return $this->render('admin/mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }

    #[Route('/admin/mail/AskPropertyInfo', name: 'op_admin_mail_AskPropertyInfo')]
    public function AskPropertyInfo(): Response
    {

    }
}
