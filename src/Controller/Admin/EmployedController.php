<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Property;
use App\Form\Admin\EmployedType;
use App\Form\Admin\ResettingPasswordType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/opadmin/employed')]
class EmployedController extends AbstractController
{

    #[Route('/api/users', name: 'op_admin_employeds')]
    public function employeds(Request $request, EmployedRepository $repository)
    {
        return $this->json($repository->search($request->query->get('e')));
    }

    #[Route('/AllEmployed', name: 'op_admin_employeds_allEmployed', methods: ['GET'])]
    public function AllEmployed(Request $request, EmployedRepository $employedRepository)
    {
        return $this->render('webapp/page/employed/allemployed.html.twig', [
            'employeds' => $employedRepository->findBy(['isWebpublish'=>1]),
        ]);
    }


    #[Route('/', name: 'op_admin_employed_index', methods: ['GET'])]
    public function index(EmployedRepository $employedRepository): Response
    {
        return $this->render('admin/employed/index.html.twig', [
            'employeds' => $employedRepository->findAll(),
        ]);
    }

    #[Route('/changepassword/{id}', name: 'op_admin_employed_changepassword', methods: ['GET'])]
    public function changePassword(Employed $employed){

    }

    #[Route('/new', name: 'op_admin_employed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmployedRepository $employedRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $employed = new Employed();
        $form = $this->createForm(EmployedType::class, $employed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employed->setPassword($userPasswordHasher->hashPassword($employed,'papsimmo'));
            $employedRepository->add($employed);
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

    #[Route('/withproperty/{id}', name: 'op_admin_employed_showwithproperty', methods: ['GET'])]
    public function showwithproperty(Employed $employed, PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->listPropertiesPublishByEmployed($employed->getId());
        //dd($properties);

        return $this->render('admin/employed/showwithproperties.html.twig', [
            'employed' => $employed,
            'properties' => $properties
        ]);

    }

    #[Route('/{id}/edit', name: 'op_admin_employed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employed $employed, EmployedRepository $employedRepository): Response
    {
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

    #[Route('/{id}/adminresetpassword', name: 'op_admin_employed_adminresetpassword', methods: ['GET', 'POST'])]
    public function adminResetPassword(Request $request, Employed $employed, EmployedRepository $employedRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(ResettingPasswordType::class, $employed);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $userPasswordHasher->hashPassword($employed, $form->get('password')->getData());
            $employed->setPassword($password);
            $employedRepository->add($employed);

            $request->getSession()->getFlashBag()->add('success', "le mot de passe a été renouvelé.");

            return $this->redirectToRoute('op_admin_employed_index');

        }

        return $this->render('admin/employed/adminresettingpassword.html.twig', [
            'form' => $form->createView(),
            'employed' => $employed

        ]);
    }
}
