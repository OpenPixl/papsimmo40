<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Contact;
use App\Form\Admin\ContactType;
use App\Repository\Admin\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/opadmin/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'op_admin_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_admin_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact);
            return $this->redirectToRoute('op_admin_contact_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/showbyemployed/{idEmployed}', name: 'op_admin_contact_showbyemployed', methods: ['GET'])]
    public function showByEmployed(ContactRepository $contactRepository, $idEmployed): Response
    {
        $contacts = $contactRepository->findBy(array('employed' => $idEmployed));
        return $this->render('admin/contact/showbyemployed.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $form = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('op_admin_contact_edit', ['id' => $contact->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact);
            return $this->redirectToRoute('op_admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editByEmployed', name: 'op_admin_contact_editByEmployed', methods: ['GET', 'POST'])]
    public function editByEmployed(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contactRepository->add($contact);
            return $this->redirectToRoute('op_admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }
        //dd($contact);
        return $this->renderForm('admin/contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact);
        }

        return $this->redirectToRoute('op_admin_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
