<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(
        protected RequestStack $request,
    ){}

    public function createEmail($email_exp, $email_name, $email_dest)
    {
        $request = $this->request->getCurrentRequest();

        $email = (new TemplatedEmail())
            ->from(new Address($email_exp, $email_name))
            ->to($email_dest)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("[PAPs Immo] : Erreur sur le document présenté")
            ->htmlTemplate('admin/mail/messageErrorDocument.html.twig')
            ->context([
                'transaction' => $transaction,
                'url' => $request->server->get('HTTP_HOST'),
                'typedoc' => $typeDoc
            ]);

        return $email;
    }
}