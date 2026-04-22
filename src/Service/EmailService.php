<?php

namespace App\Service;

use App\Entity\Admin\Application;
use App\Entity\Gestapp\Transaction;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Gestapp\TransactionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{

    public function __construct(
        public TransactionRepository $transactionRepository,
        protected MailerInterface $mailer,
        protected RequestStack $request,
        protected UrlGeneratorInterface $router
    ){}

    public function submitEmailFromTransac($email_expediteur, $expediteur_name, $email_destinataire, $subject, $idtransaction){

        $transaction = $this->transactionRepository->find($idtransaction);
        $state = preg_split("/[\s|]+/", $transaction->getState())[0];
        $typeDoc = preg_split("/[\s|]+/", $transaction->getState())[1];

        $email = (new TemplatedEmail())
            ->from(new Address($email_expediteur, $expediteur_name))
            ->to($email_destinataire)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject);
        if($state == 'Invalidation'){
            $email
                ->htmlTemplate('admin/mail/messageErrorDocument.html.twig')
                ->context([
                    'transaction' => $transaction,
                    'typeDoc' => $typeDoc,
                    'url' => $this->router->generate('op_webapp_public_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ]);
            ;
        }
        else if($state == 'Annulation'){
            $email
                ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                ->context([
                    'transaction' => $transaction,
                    'typeDoc' => $typeDoc,
                    'url' => $this->router->generate('op_webapp_public_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ]);
            ;
        }
        else{
            $email
                ->htmlTemplate('admin/mail/messageTransaction.html.twig')
                ->context([
                    'transaction' => $transaction,
                    'url' => $this->router->generate('op_webapp_public_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ]);
            ;
        };

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            dd($e);
        }
    }
}