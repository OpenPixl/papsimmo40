<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\Project;
use App\Form\Gestapp\ProjectType;
use App\Repository\Gestapp\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('gestapp/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_gestapp_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->add($project);
            return $this->redirectToRoute('op_gestapp_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('gestapp/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->add($project);
            return $this->redirectToRoute('op_gestapp_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project);
        }

        return $this->redirectToRoute('op_gestapp_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
