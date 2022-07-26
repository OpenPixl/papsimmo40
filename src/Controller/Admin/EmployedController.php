<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Contact;
use App\Entity\Admin\Employed;
use App\Form\Admin\EmployedType;
use App\Repository\Admin\ContactRepository;
use App\Repository\Admin\EmployedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/opadmin/employed')]
class EmployedController extends AbstractController
{

    #[Route('/api/users', name: 'op_admin_employeds')]
    public function employeds(Request $request, EmployedRepository $repository)
    {
        return $this->json($repository->search($request->query->get('e')));
    }

    #[Route('/', name: 'op_admin_employed_index', methods: ['GET'])]
    public function index(EmployedRepository $employedRepository): Response
    {
        return $this->render('admin/employed/index.html.twig', [
            'employeds' => $employedRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_admin_employed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmployedRepository $employedRepository, ContactRepository $contactRepository): Response
    {
        $employed = new Employed();
        $form = $this->createForm(EmployedType::class, $employed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employedRepository->add($employed);
            $contact = new Contact();
            $contact->setEmployed($employed);
            $contact->setGsm('00.00.00.00.00');
            $contactRepository->add($contact);
            return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/employed/new.html.twig', [
            'employed' => $employed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_employed_show', methods: ['GET'])]
    public function show(Employed $employed): Response
    {
        return $this->render('admin/employed/show.html.twig', [
            'employed' => $employed,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_employed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employed $employed, EmployedRepository $employedRepository, ContactRepository $contactRepository): Response
    {
        $contact = $contactRepository->findOneBy(['employed' => $employed->getId()]);
        $form = $this->createForm(EmployedType::class, $employed, [
            'action'=>$this->generateUrl('op_admin_employed_edit', ['id' => $employed->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employedRepository->add($employed);
            return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/employed/edit.html.twig', [
            'employed' => $employed,
            'form' => $form,
            'contact' => $contact
        ]);
    }

    #[Route('/{id}', name: 'op_admin_employed_delete', methods: ['POST'])]
    public function delete(Request $request, Employed $employed, EmployedRepository $employedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employed->getId(), $request->request->get('_token'))) {
            $employedRepository->remove($employed);
        }

        return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
    }
}
