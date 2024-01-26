<?php

namespace App\Controller\Admin;

use App\Entity\Gestapp\Transaction;
use App\Repository\Gestapp\TransactionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class MailerController extends AbstractController
{
    #[Route('/admin/mailer', name: 'app_admin_mailer')]
    public function index(): Response
    {
        return $this->render('admin/mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }

    #[Route('/admin/mailer/pdfpromisetransaction/{id}', name: 'op_admin_mailer_pdfpromisetransaction')]
    public function pdfpromisetransaction(MailerInterface $mailer, Transaction $transaction): Response
    {
        $email = (new TemplatedEmail())
            ->from(new Address('contact@papsimmo.com', 'SoftPAPs'))
            ->to('xavier.burke@openpixl.fr')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('[PAPs Immo] : Un document de transaction attend votre approbation')
            ->htmlTemplate('admin/mail/messageTransaction.html.twig')
            ->context([
                'transaction' => $transaction,
            ]);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            dd($e);
        }
        return $this->render('admin/mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }
}
