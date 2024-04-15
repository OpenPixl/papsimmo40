<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Property;
use App\Form\Admin\EmployedType;
use App\Form\Admin\ResettingPasswordType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\PropertyRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EmployedController extends AbstractController
{

    #[Route('/opadmin/employed/api/users', name: 'op_admin_employeds')]
    public function employeds(Request $request, EmployedRepository $repository)
    {
        return $this->json($repository->search($request->query->get('e')));
    }

    #[Route('/opadmin/employed/AllEmployed', name: 'op_admin_employeds_allEmployed', methods: ['GET'])]
    public function AllEmployed(Request $request, EmployedRepository $employedRepository)
    {
        return $this->render('webapp/page/employed/allemployed.html.twig', [
            'employeds' => $employedRepository->publishEmployedOnApp(),
        ]);
    }

    #[Route('/opadmin/employed/findPrescribers', name: 'op_admin_employeds_findPrescribers', methods: ['GET'])]
    public function findPrescribers(Request $request, EmployedRepository $employedRepository)
    {
        return $this->render('webapp/page/employed/allemployed.html.twig', [
            'employeds' => $employedRepository->publishEmployedOnApp(),
        ]);
    }

    #[Route('/opadmin/employed/', name: 'op_admin_employed_index', methods: ['GET'])]
    public function index(EmployedRepository $employedRepository): Response
    {
        $admins = $employedRepository->listRole('["ROLE_SUPER_ADMIN"]');
        $employeds = $employedRepository->listRole('["ROLE_EMPLOYED"]');
        $listeEmployeds = [];
        foreach ($admins as $a){
            array_push($listeEmployeds, $a);
        }
        foreach ($employeds as $e){
            array_push($listeEmployeds, $e);
        }
        //dd($listeEmployeds);
        return $this->render('admin/employed/index.html.twig', [
            'employeds' => $employedRepository->findAll(),
        ]);
    }

    #[Route('/opadmin/selectemployed/', name: 'op_admin_employed_selectemployed', methods: ['GET'])]
    public function selectemployed(EmployedRepository $employedRepository): Response
    {
        $employeds = $employedRepository->selectEmployed();
        return $this->json([
            'code' => '200',
            'employeds' => $employeds
        ],200);
    }

    #[Route('/opadmin/employed/changepassword/{id}', name: 'op_admin_employed_changepassword', methods: ['GET'])]
    public function changePassword(Employed $employed){

    }

    #[Route('/opadmin/employed/new', name: 'op_admin_employed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, EmployedRepository $employedRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $employed = new Employed();
        $form = $this->createForm(EmployedType::class, $employed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // intégration d'une image
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $originalavatarFileName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeavatarFileName = $slugger->slug($originalavatarFileName);
                $newavatarFileName = $safeavatarFileName . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newavatarFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $employed->setAvatarName($newavatarFileName);
                $employed->setAvatarSize($avatarFile->getSize());
            }

            $plainpassword = explode("@", $form->get('email')->getData());
            $numCollaborator = rand(0,10).rand(0,10).rand(0,10).rand(0,10).rand(0,10).rand(0,10);
            $employed->setPassword($userPasswordHasher->hashPassword($employed,$plainpassword[0]));
            $employed->setNumCollaborator($numCollaborator);
            $employed->setRoles(["ROLE_EMPLOYED"]);
            $employedRepository->add($employed);

            return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employed/new.html.twig', [
            'employed' => $employed,
            'form' => $form,
        ]);
    }

    #[Route('/opadmin/employed/{id}', name: 'op_admin_employed_show', methods: ['GET'])]
    public function show(Employed $employed): Response
    {
        return $this->render('admin/employed/show.html.twig', [
            'employed' => $employed,
        ]);
    }

    #[Route('/webapp/withproperty/{id}', name: 'op_admin_employed_showwithproperty', methods: ['GET'])]
    public function showwithproperty(Employed $employed, PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->listPropertiesPublishByEmployed($employed->getId());
        //dd($properties);

        return $this->render('admin/employed/showwithproperties.html.twig', [
            'employed' => $employed,
            'properties' => $properties
        ]);

    }

    #[Route('/opadmin/employed/isactiv/{id}', name: 'op_admin_employed_isactiv', methods: ['POST'])]
    public function isactiv(Employed $employed, EmployedRepository $employedRepository): Response
    {
        $verified = $employed->isVerified();

        if($verified == true){
            $isActiv = "désactivé";
            $employed->setIsVerified(0);
        }else{
            $isActiv = "activé";
            $employed->setIsVerified(1);
        }
        $employedRepository->add($employed, true);
        $employeds = $employedRepository->findAll();

        return $this->json([
            'code' => 200,
            'message' => "Le compte du collaborateur est actuellement <b>" . $isActiv. "</b>.",
            'liste' => $this->renderView('admin/employed/_list.html.twig', [
                'employeds' => $employeds
            ])
        ],200);
    }

    #[Route('/opadmin/employed/{id}/edit', name: 'op_admin_employed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SluggerInterface $slugger, Employed $employed, EmployedRepository $employedRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployedType::class, $employed, [
            'action'=>$this->generateUrl('op_admin_employed_edit', ['id' => $employed->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Suppression directe de l'avatar
            $supprAvatarInput = $form->get('isSupprAvatar')->getData();
            if($supprAvatarInput && $supprAvatarInput == true){
                // récupération du nom de l'image
                $avatarName = $employed->getAvatarName();
                $pathheader = $this->getParameter('avatars_directory').'/'.$avatarName;
                // On vérifie si l'image existe
                if(file_exists($pathheader)){
                    unlink($pathheader);
                }
                $employed->setAvatarName(null);
                $employed->setIsSupprAvatar(0);
            }

            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                // Effacement du fichier bannièreFileName si il est présent en BDD
                // récupération du nom de l'image
                $avatarName = $employed->getAvatarName();
                // suppression du Fichier
                if($avatarName){
                    $pathlogo = $this->getParameter('avatars_directory').'/'.$avatarName;
                    // On vérifie si l'image existe
                    if(file_exists($pathlogo)){
                        unlink($pathlogo);
                    }
                }

                $originalavatarFileName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeavatarFileName = $slugger->slug($originalavatarFileName);
                $newavatarFileName = $safeavatarFileName . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newavatarFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $employed->setAvatarName($newavatarFileName);
            }

            $entityManager->flush();
            return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employed/edit.html.twig', [
            'employed' => $employed,
            'form' => $form,
        ]);
    }

    #[Route('/opadmin/employed/{id}', name: 'op_admin_employed_delete', methods: ['POST'])]
    public function delete(Request $request, Employed $employed, EmployedRepository $employedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employed->getId(), $request->request->get('_token'))) {
            $employedRepository->remove($employed);
        }

        return $this->redirectToRoute('op_admin_employed_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}', name: 'op_admin_employed_del', methods: ['POST'])]
    public function del(Request $request, Employed $employed,EmployedRepository $employedRepository): Response
    {
        $user = $this->getUser();
        $admin = $employedRepository->find($user->getId());

        $Roles = $employed->getRoles();
        if(in_array('ROLE_SUPER_ADMIN',$Roles)){
            $employeds = $employedRepository->findAll();

            return $this->json([
                'code' => '200',
                'message' => "L'utilisateur ne peut pas être supprimé de la plateforme.
                            <br>Il s'agit d'un compte Administrateur.",
                'liste' => $this->renderView('admin/employed/_list.html.twig', [
                    'employeds' => $employeds
                ])
            ],200);
        }else{
            // Action sur les clients de l'user
            $clients = $employed->getCustomer();
            foreach ($clients as $client){
                $employed->removeCustomer($client);
                $admin->addCustomer($client);
            }
            // Action sur les propriétés de l'user
            $properties = $employed->getProperties();
            foreach ($properties as $property){
                $employed->removeProperty($property);
                $admin->addProperty($property);
            }
            // Action sur les Articles de l'user
            $articles = $employed->getArticles();
            foreach ($articles as $article){
                $employed->removeArticle($article);
                $admin->addArticle($article);
            }
            // Action sur les sections de l'user
            $sections = $employed->getSections();
            foreach ($sections as $section){
                $employed->removeSection($section);
                $admin->addSection($section);
            }
            // Action sur les Pages de l'user
            $pages = $employed->getPages();
            foreach ($pages as $page){
                $employed->removePage($page);
                $admin->addPage($page);
            }
            // Action sur les Contacts de l'user
            $contacts = $employed->getContacts();
            foreach ($contacts as $contact){
                $employed->removeContact($contact);
            }
            // Action sur les Pages de l'user
            $projects = $employed->getProjects();
            foreach ($projects as $project){
                $employed->removeProject($project);
                $admin->addProject($project);
            }

            $employedRepository->remove($employed);

            $employeds = $employedRepository->findAll();

            return $this->json([
                'code' => '200',
                'message' => "L'utilisateur a été correctement supprimé de la plateforme.<br>Tous les éléments créer par ce dernier vous ont été réattribué.",
                'liste' => $this->renderView('admin/employed/_list.html.twig', [
                    'employeds' => $employeds
                ])
            ],200);
        }
    }

    #[Route('/opadmin/employed/{id}/adminresetpassword', name: 'op_admin_employed_adminresetpassword', methods: ['GET', 'POST'])]
    public function adminResetPassword(
        Request $request,
        Employed $employed,
        EmployedRepository $employedRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(ResettingPasswordType::class, $employed);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //dd($form);
            $password = $userPasswordHasher->hashPassword($employed, $form->get('password')->getData());

            $employed->setPassword($password);
            $em->persist($employed);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "le mot de passe a été renouvelé.");

            return $this->redirectToRoute('op_admin_employed_index');

        }

        return $this->render('admin/employed/adminresettingpassword.html.twig', [
            'form' => $form->createView(),
            'employed' => $employed

        ]);
    }
}
