<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Contact;
use App\Form\Admin\ContactSupportType;
use App\Repository\Admin\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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

    #[Route('/admin/mail/contactsupport', name: 'op_admin_mail_contactsupport')]
    public function contactsupport(Request $request, ContactRepository $contactRepository, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactSupportType::class, $contact,[
            'method' => 'POST',
            'action' => $this->generateUrl('op_admin_mail_contactsupport')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            $email = (new Email())
                ->from($contact->getEmail())
                ->to('xavier.burke@openpixl.fr')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('[PAPs Immo] : Nouvelle demande de support')
                ->text($contact->getContent());

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }

            return $this->redirectToRoute('op_webapp_public_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/mail/support.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
