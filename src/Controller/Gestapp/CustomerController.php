<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\CustomerChoice;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Form\Gestapp\CustomerType;
use App\Form\SearchCustomersType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\CustomerChoiceRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PropertyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestapp/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository,PaginatorInterface $paginator, Request  $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        if($hasAccess == true){
            // on liste topus les clients quelques soit les utilisateurs
            $data = $customerRepository->findAllCustomer();

            $customers = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('gestapp/customer/index.html.twig', [
                'customers' => $customers,
                'user' => $user
            ]);
        }else{
            $data = $customerRepository->findAllCustomerByEmployed(['refEmployed' => $user]);
            $customers = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('gestapp/customer/index.html.twig', [
                'customers' => $customers,
                'user' => $user
            ]);
        }
    }

    #[Route('/byproperty/{id}', name: 'op_gestapp_customer_listbyproperty', methods: ['GET'])]
    public function listByProperty(CustomerRepository $customerRepository, Property $property, Request $request): Response
    {
        // intégration dans ce controller du formulaire de recherche des clients
        return $this->render('gestapp/customer/listByProperty.html.twig', [
            'customers' => $customerRepository->listByProperty($property),
            'property' => $property,
        ]);
    }

    #[Route('/byproperty/searchcustomer/{idproperty}', name: 'op_gestapp_customer_searchcustomer', methods: ['GET', 'POST'])]
    public function listsearchcustomer(CustomerRepository $customerRepository, Request $request, $idproperty): Response
    {
        // récupération de la liste
        $customers = $customerRepository->findBy(['id'=>0]);

        $form = $this->createForm(SearchCustomersType::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $customers = $customerRepository->SearchCustomers($search->get('word')->getData());
            return $this->json([
                'code'=> 200,
                'message' => "La recherche à aboutie",
                'liste' => $this->renderView('gestapp/customer/search/_liste.html.twig', [
                    'customers' => $customers,
                    'idproperty' => $idproperty
                ])
            ]);
        }
        // intégration dans ce controller du formulaire de recherche des clients

        return $this->render('gestapp/customer/search/_listsearch.html.twig', [
            'customers' => $customers,
            'idproperty' => $idproperty,
            'form' => $form->createView()
        ]);
    }

    #[Route('/byproperty/addsearchcustomer/{id}/{idproperty}', name: 'op_gestapp_customer_addsearchcustomer', methods: ['POST'])]
    public function addSearchCustomer(Customer $customer, CustomerRepository $customerRepository, $idproperty, PropertyRepository $propertyRepository)
    {
        $property = $propertyRepository->find($idproperty);
        $property->addCustomer($customer);
        $propertyRepository->add($property);

        $customers = $customerRepository->listByProperty($property);

        // correction à apporter
        return $this->json([
            'code'=> 200,
            'message' => "Le vendeur a été ajouté",
            'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                'customers' => $customers,
                'idproperty' => $idproperty
            ])
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

    #[Route('/addcustomerjson/{idproperty}', name: 'op_gestapp_customer_addcustomerjson',  methods: ['GET', 'POST'])]
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
        // Récupération des données stockées
        $data = json_decode($request->getContent(), true);

        // Contruction de la référence pour chaque propriété
        $date = new \DateTime();
        $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($data['lastName'], 0,4 );
        //dd($employed, $property, $data);

        $customer = new Customer();
        $customer->setRefCustomer($refCustomer);
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setAdress($data['adress']);
        $customer->setComplement($data['complement']);
        $customer->setZipCode($data['zipcode']);
        $customer->setCity($data['city']);
        $customer->setHome($data['home']);
        $customer->setDesk($data['desk']);
        $customer->setGsm($data['gsm']);
        $customer->setFax($data['fax']);
        $customer->setOtherEmail($data['otherEmail']);
        $customer->setFacebook($data['facebook']);
        $customer->setInstagram($data['instagram']);
        $customer->setLinkedin($data['linkedin']);
        $customer->setRefEmployed($employed);
        $customer->setCustomerChoice($customerChoice);
        $customer->addProperty($property);

        $customerRepository->add($customer);

        $customers = $customerRepository->listbyproperty($property);
        //dd($liste);

        return $this->json([
            'code'=> 200,
            'message' => "Le vendeurs a été correctement ajouté.",
            'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                'customers' => $customers,
                'idproperty' => $idproperty
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

    #[Route('/getformcustomer/{id}', name: 'op_gestapp_customer_getform', methods: ['GET'])]
    public function getFormCustomer(Customer $customer,Request $request)
    {
        $form = $this->createForm(CustomerType::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_getform', ['id'=> $customer->getId()]),
            'method'=>'POST',
            'attr' => ['class'=>'formEditCustomer']
        ]);
        $form->handleRequest($request);
        return $this->json([
            'code'=> 200,
            'form' => $this->renderForm('gestapp/customer/_form.html.twig', [
                'customer' => $customer,
                'form' => $form
            ])
        ], 200);
    }

    #[Route('/editcustomerjson/{id}/{idproperty}', name: 'op_gestapp_customer_editcustomerjson',  methods: ['GET', 'POST'])]
    public function editCustomerJson(
        Request $request,
        Customer $customer,
        $idproperty,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        CustomerChoiceRepository $customerChoiceRepository,
    )
    {
        $property = $propertyRepository->find($idproperty);
        $form = $this->createForm(CustomerType::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_editcustomerjson', [
                'id'=> $customer->getId(),
                'idproperty' => $idproperty
            ]),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);
        //dd($form->isValid());
        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            $customers = $customerRepository->listbyproperty($idproperty);
            return $this->json([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
                'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                    'customers' => $customers,
                    'property' => $property,
                    'idproperty' => $idproperty
                ])
            ], 200);

        }

        dd($form->getErrors());
        $customers = $customerRepository->listbyproperty($idproperty);
        return $this->json([
            'code'=> 200,
            'message' => "Le vendeur a doit être correctement renseigné.",
            'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                'customers' => $customers,
                'property' => $property,
                'idproperty' => $idproperty
            ])
        ], 200);



        dd($customers);


    }

    #[Route('/{id}', name: 'op_gestapp_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer);
        }

        return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/{idproperty}', name: 'op_gestapp_customer_del_onproperty', methods: ['POST'])]
    public function delOnProperty(Customer $customer, $idproperty, PropertyRepository $propertyRepository,CustomerRepository $customerRepository)
    {
        // suprression du client vendeur sur le bien
        $property = $propertyRepository->find($idproperty);
        $property->removeCustomer($customer);
        $propertyRepository->add($property);
        //récupératuion de la liste de teous les client sur le bien
        $customers = $customerRepository->listbyproperty($property);
        return $this->json([
            'code'=> 200,
            'message' => "Le vendeurs a été correctement ajouté.",
            'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                'customers' => $customers,
                'idproperty' => $idproperty
            ])
        ], 200);

    }

}
