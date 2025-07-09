<?php

namespace App\Controller\Gestapp\Customer;

use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Customer\Research;
use App\Form\Gestapp\Customer\ResearchType;
use App\Repository\Gestapp\Customer\ResearchRepository;
use App\Repository\Gestapp\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/customer/research')]
final class ResearchController extends AbstractController
{
    #[Route(name: 'op_gestapp_customer_research_index', methods: ['GET'])]
    public function index(ResearchRepository $researchRepository): Response
    {
        return $this->render('gestapp/customer/research/index.html.twig', [
            'research' => $researchRepository->findAll(),
        ]);
    }

    #[Route('/new/{idCustomer}', name: 'op_gestapp_customer_research_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager,
        $idCustomer): Response
    {
        $research = new Research();
        $form = $this->createForm(ResearchType::class, $research, [
            'action' => $this->generateUrl('op_gestapp_customer_research_new', ['idCustomer' => $idCustomer]),
            'method' => 'POST',
            'attr' => [
                'id' => 'Form_Customer_Research',
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer = $customerRepository->find($idCustomer);
            $research->setCustomer($customer);

            $entityManager->persist($research);
            $entityManager->flush();

            $customer = $entityManager->getRepository(Customer::class)->find($idCustomer);

            return $this->json([
                'code' => 200,
                'liste' => $this->renderView('gestapp/customer/research/include/_liste.html.twig',[
                    'customer' => $customer,
                ]),
                'message' => 'la recherche été ajouter au dossier du client.',
            ],200);
        }

        $view = $this->render('gestapp/customer/research/new.html.twig', [
            'research' => $research,
            'form' => $form,
        ]);

        return $this->json([
            'code' => 200,
            'formView' => $view->getContent(),
        ],200);
    }

    #[Route('/{id}', name: 'op_gestapp_customer_research_show', methods: ['GET'])]
    public function show(Research $research): Response
    {
        return $this->render('gestapp/customer/research/show.html.twig', [
            'research' => $research,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_customer_research_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Research $research, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResearchType::class, $research, [
            'action' => $this->generateUrl('op_gestapp_customer_research_edit', ['id' => $research->getId()]),
            'method' => 'POST',
            'attr' => [
                'id' => 'Form_Customer_Research',
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $idCustomer = $research->getCustomer()->getId();;

            $customer = $entityManager->getRepository(Customer::class)->find($idCustomer);

            return $this->json([
                'code' => 200,
                'liste' => $this->renderView('gestapp/customer/research/include/_liste.html.twig',[
                    'customer' => $customer,
                ]),
                'message' => 'la recherche été ajouter au dossier du client.',
            ],200);
        }

        $view = $this->render('gestapp/customer/research/new.html.twig', [
            'research' => $research,
            'form' => $form,
        ]);

        return $this->json([
            'code' => 200,
            'form' => $view->getContent(),
        ],200);
    }

    #[Route('/{id}', name: 'op_gestapp_customer_research_delete', methods: ['POST'])]
    public function delete(Request $request, Research $research, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$research->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($research);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_gestapp_customer_research_index', [], Response::HTTP_SEE_OTHER);
    }
}
