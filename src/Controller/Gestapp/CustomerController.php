<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\CustomerChoice;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Form\Gestapp\CustomerType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\CustomerChoiceRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('gestapp/customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    #[Route('/byproperty/{property}', name: 'op_gestapp_customer_listbyproperty', methods: ['GET'])]
    public function listByProperty(CustomerRepository $customerRepository, Property $property): Response
    {
        $idproperty = $property->getId();
        //dd($property);
        return $this->render('gestapp/customer/listByProperty.html.twig', [
            'customers' => $customerRepository->listByProperty($property),
            'idproperty' => $idproperty
        ]);
    }

    #[Route('/new', name: 'op_gestapp_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/addcustomer/{idproperty}', name: 'op_gestapp_customer_addcustomerjson',  methods: ['GET', 'POST'])]
    public function addCustomerJson(
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        $idproperty
    )
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        $property = $propertyRepository->find($idproperty);
        $customerChoice = $customerChoiceRepository->find(1);

        $data = json_decode($request->getContent(), true);
        //dd($employed, $property, $data);

        $customer = new Customer();
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setAdress($data['adress']);
        $customer->setComplement($data['complement']);
        $customer->setZipCode($data['zipcode']);
        $customer->setCity($data['city']);
        $customer->setRefEmployed($employed);
        $customer->setCustomerChoice($customerChoice);
        $customer->addProperty($property);

        $customerRepository->add($customer);

        $customers = $customerRepository->findBy(array('properties'=>array($idproperty)));
        //dd($liste);

        return $this->json([
            'code'=> 200,
            'message' => "Le vendeurs a été correctement ajouté.",
            'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                'customers' => $customers
            ])
        ], 200);
    }

    #[Route('/addcustomer', name: 'op_gestapp_customer_addcustomer',  methods: ['GET', 'POST'])]
    public function addCustomer(
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
    )
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->json([
                'code'=> 200,
                'message' => "Le vendeurs a été correctement ajouté."
            ], 200);
        }

        return $this->renderForm('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/serchCustomers', name: 'op_gestapp_customers_searchcustomer', methods: ['POST'])]
    public function searchCustomer()
    {

    }

    #[Route('/{id}', name: 'op_gestapp_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->render('gestapp/customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer);
        }

        return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
