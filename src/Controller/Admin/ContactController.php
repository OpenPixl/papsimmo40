<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Property;
use App\Form\Admin\ContactType;
use App\Repository\Admin\ContactRepository;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\PropertyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'op_admin_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $contacts = $contactRepository->findAll();

        return $this->render('admin/contact/index.html.twig',[
            'allcontacts' => $contacts
        ]);
    }

    #[Route('/listAllContacts', name: 'op_admin_contact_listallcontacts', methods: ['GET'])]
    public function listAllContacts(ContactRepository $contactRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $allcontacts = $contactRepository->findAll();
        $pagAllContacts = $paginator->paginate(
            $allcontacts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'liste' => $this->renderView('admin/contact/_listallcontacts.html.twig', [
                'allcontacts' => $allcontacts
            ])
        ], 200);
    }

    #[Route('/listPropertiesContacts', name: 'op_admin_contact_listpropertiescontacts', methods: ['GET'])]
    public function listPropertiesContacts(ContactRepository $contactRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $propertycontacts = $contactRepository->listPropertiesContacts($user);
        $pagPropertyContacts = $paginator->paginate(
            $propertycontacts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'liste' => $this->renderView('admin/contact/_listpropertycontacts.html.twig', [
                'propertycontacts' => $pagPropertyContacts,
            ])
        ], 200);
    }

    /***
     * Envoie d'un message depuis le Footer de la page.
     *
     * @param Request $request
     * @param ContactRepository $contactRepository
     * @param MailerInterface $mailer
     * @return Response
     */
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

            return $this->redirectToRoute('op_webapp_public_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contact/new.html.twig', [
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

    #[Route('/del/{id}', name: 'op_admin_contact_del', methods: ['POST'])]
    public function del(Request $request, Contact $contact, ContactRepository $contactRepository)
    {
        $forEmployed = $contact->getForEmployed();
        $idProperty = $contact->getProperty();
        //dd($forEmployed, $idProperty);
        if($forEmployed){
            $contact->getForEmployed()->removeContact($contact);
        }
        if($idProperty){
            $contact->getProperty()->removeContact($contact);
        }
        $contactRepository->remove($contact, true);


        $allcontacts = $contactRepository->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "La photo du bien a été correctement modifiée.",
            'listallcontact' => $this->renderView('admin/contact/_listallcontacts.html.twig', [
                'allcontacts' => $allcontacts
            ])
        ], 200);
    }

    #[Route('/AskPropertyInfo/{idproperty}', name: 'op_admin_contact_askpropertyinfo', methods: ['GET', 'POST'])]
    public function AskPropertyInfo(Request $request, ContactRepository $contactRepository, MailerInterface $mailer, $idproperty, PropertyRepository $propertyRepository): Response
    {
        // récupération info property
        $property = $propertyRepository->find($idproperty);
        $employed = $property->getRefEmployed();

        $contact = new Contact();
        $contact->setForEmployed($employed);
        $contact->setContent("Bonjour,
Je souhaiterais avoir plus de renseignements sur le bien \"" . $property->getName() . "\" et prendre rendez-vous pour le visiter.
Pourriez-vous me recontacter ?
Cordialement");
        $contact->setProperty($property);
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
                ->to($employed->getEmail()) // Mettre en fin de test le code pour l'utilisateur courant
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('[PAPs Immo] : Nouvelle demande de contact depuis votre site pour le bien référencé :' . $property->getRef())
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

    #[Route('/contactOffline', name: 'op_admin_contact_offline', methods: ['GET', 'POST'])]
    public function OfflineContact(ContactRepository $contactRepository, Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact,[
            'method' => 'POST',
            'action' => $this->generateUrl('op_admin_contact_offline')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->isvalid);
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

            return $this->redirectToRoute('op_admin_contact_offline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/contact/postcontactoffline.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
