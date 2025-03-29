<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Application;
use App\Form\Admin\ApplicationType;
use App\Repository\Admin\ApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/opadmin/application')]
class ApplicationController extends AbstractController
{
    #[Route('/', name: 'op_admin_application_index', methods: ['GET'])]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('admin/application/index.html.twig', [
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_admin_application_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ApplicationRepository $applicationRepository,
        SluggerInterface $slugger
    ): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // intégration du code du logo du client
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safelogoFileName = $slugger->slug($originalFilename);
                $newlogoFileName = $safelogoFileName . '-' . '.' . $logoFile->guessExtension();
                $pathdir = $this->getParameter('application_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $logoFile->move(
                            $this->getParameter('application_directory'),
                            $newlogoFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $logoFile->move(
                            $this->getParameter('application_directory'),
                            $newlogoFileName
                        );
                    }


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $application->setLogoName($newlogoFileName);
                $application->setLogoSize($logoFile->getSize());;
            }
            // integration de la favicon du client
            $faviconFile = $form->get('faviconFile')->getData();
            if ($faviconFile) {
                $originalFilename = pathinfo($faviconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safefaviconFileName = $slugger->slug($originalFilename);
                $newfaviconFileName = $safefaviconFileName . '-' . '.' . $faviconFile->guessExtension();
                $pathdir = $this->getParameter('application_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $faviconFile->move(
                            $this->getParameter('application_directory'),
                            $newfaviconFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $faviconFile->move(
                            $this->getParameter('application_directory'),
                            $newfaviconFileName
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $application->setfaviconName($newfaviconFileName);
                $application->setfaviconSize($logoFile->getSize());
            }
            $applicationRepository->add($application);
            return $this->redirectToRoute('op_admin_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/application/new.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_application_show', methods: ['GET'])]
    public function show(Application $application): Response
    {
        return $this->render('admin/application/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_admin_application_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Application $application,
        ApplicationRepository $applicationRepository,
        SluggerInterface $slugger
    ): Response
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // intégration du code du logo du client
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safelogoFileName = $slugger->slug($originalFilename);
                $newlogoFileName = $safelogoFileName . '-' . '.' . $logoFile->guessExtension();
                $pathdir = $this->getParameter('application_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $logoFile->move(
                            $this->getParameter('application_directory'),
                            $newlogoFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $logoFile->move(
                            $this->getParameter('application_directory'),
                            $newlogoFileName
                        );
                    }


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $application->setLogoName($newlogoFileName);
                //$application->setLogoSize($logoFile->getSize());;
            }
            // integration de la favicon du client
            $faviconFile = $form->get('faviconFile')->getData();
            if ($faviconFile) {
                $originalFilename = pathinfo($faviconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safefaviconFileName = $slugger->slug($originalFilename);
                $newfaviconFileName = $safefaviconFileName . '-' . '.' . $faviconFile->guessExtension();
                $pathdir = $this->getParameter('application_directory');
                // Move the file to the directory where brochures are stored
                try {
                    if (is_dir($pathdir)){
                        $faviconFile->move(
                            $this->getParameter('application_directory'),
                            $newfaviconFileName
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $faviconFile->move(
                            $this->getParameter('application_directory'),
                            $newfaviconFileName
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $application->setfaviconName($newfaviconFileName);
                //$application->setfaviconSize($logoFile->getSize());
            }

            $applicationRepository->add($application);
            return $this->redirectToRoute('op_admin_application_edit', [
                'id' => $application->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/application/edit.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_admin_application_delete', methods: ['POST'])]
    public function delete(Request $request, Application $application, ApplicationRepository $applicationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$application->getId(), $request->request->get('_token'))) {
            $applicationRepository->remove($application);
        }

        return $this->redirectToRoute('op_admin_application_index', [], Response::HTTP_SEE_OTHER);
    }
}
