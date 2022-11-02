<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Contact;
use App\Form\Admin\ContactType;
use App\Repository\Admin\ContactRepository;
use App\Repository\Gestapp\PropertyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'op_admin_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true){
            // on liste tous les clients quelques soit les utilisateurs
            $data = $contactRepository->findAllContact();
            $contacts = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('admin/contact/index.html.twig', [
                'contacts' => $contacts,
            ]);
        }else{

        }
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_admin_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact,[
            'method' => 'POST',
            'action' => $this->generateUrl('op_admin_contact_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            $email = (new Email())
                ->from($contact->getEmail())
                ->to('contact@papsimmo.fr')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('[PAPs Immo] : Nouvelle demande de contact depuis votre site')
                ->text($contact->getContent());

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }

            return $this->redirectToRoute('app_admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('admin/contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            return $this->redirectToRoute('app_admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact, true);
        }

        return $this->redirectToRoute('op_admin_contact_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/AskPropertyInfo/{idproperty}', name: 'op_admin_contact_askpropertyinfo', methods: ['GET', 'POST'])]
    public function AskPropertyInfo(Request $request, ContactRepository $contactRepository, MailerInterface $mailer, $idproperty, PropertyRepository $propertyRepository): Response
    {
        // récupération info property
        $property = $propertyRepository->find($idproperty);

        $contact = new Contact();
        $contact->setContent("Bonjour,
Je souhaiterais avoir plus de renseignements sur le bien \"" . $property->getName() . "\" et prendre rendez-vous pour le visiter.
Pourriez-vous me recontacter ?
Cordialement");
        $form = $this->createForm(ContactType::class, $contact,[
            'method' => 'POST',
            'action' => $this->generateUrl('op_admin_contact_askpropertyinfo', [
                "idproperty" => $idproperty
            ])
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
                ->subject('[PAPs Immo] : Nouvelle demande de contact depuis votre site')
                ->text($contact->getContent());

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                dd($e);
            }

            return $this->json([
                'code'=> 200,
                'message' => "Votre message a été transmis à notre agent. Il vous contactera dans les plus brefs délais",
            ], 200);
        }

        return $this->renderForm('admin/contact/askpropertyinfo.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
