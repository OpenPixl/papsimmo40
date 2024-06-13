<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\CustomerChoice;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Form\Gestapp\Customer2Type;
use App\Form\Gestapp\CustomerType;
use App\Form\SearchCustomersType;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\CustomerChoiceRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Gestapp\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository,PaginatorInterface $paginator, Request  $request): Response
    {
        // on liste tous les clients quelques soit les utilisateurs
        $data = $customerRepository->findAllCustomer();

        $customers = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('gestapp/customer/index.html.twig', [
            'customers' => $customers,
        ]);
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

        $form = $this->createForm(SearchCustomersType::class, [
            'action' => $this->generateUrl('op_gestapp_customer_searchcustomer', [
                'idproperty' => $idproperty
            ]),
            'method' => 'POST'
        ]);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $customers = $customerRepository->SearchCustomers($search->get('word')->getData());
            //dd($customers);

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
            'idproperty' => $idproperty,
            'form' => $form->createView()
        ]);
    }

    #[Route('/searchcustomer/', name: 'op_gestapp_customer_search', methods: ['POST'])]
    public function searchCustomer(Request $request, CustomerRepository $customerRepository)
    {
        $data = json_decode($request->getContent(), true);
        $customers = $customerRepository->SearchCustomers($data['word']);

        return $this->json([
            'liste' => $this->renderView('gestapp/customer/search/_listeTr.html.twig', [
                'customers' => $customers,
            ])
        ], 200);
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
                'idproperty' => $idproperty,
            ])
        ]);
    }

    #[Route('/new', name: 'op_gestapp_customer_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        CustomerChoiceRepository $customerChoiceRepository,): Response
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Préparation des éléments de configuration
            $date = new \DateTime();
            $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($form->get('lastName')->getData(), 0,4 );
            // Hydratation des champs
            $customer->setRefCustomer($refCustomer);
            $customer->setRefEmployed($employed);
            // Ajout en BDD du nouveau client
            $customerRepository->add($customer);
            return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestapp/customer/new.html.twig', [
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
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        $idproperty
    )
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        $property = $propertyRepository->find($idproperty);
        $customerChoice = $customerChoiceRepository->find(1);

        $customer = new Customer();
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_addcustomerjson', [
                'idproperty' => $idproperty
            ]),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->isSubmitted(), $form->isValid());
            // Contruction de la référence pour chaque propriété
            $date = new \DateTime();
            $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($form->get('firstName')->getData(), 0,3 ).substr($form->get('lastName')->getData(), 0,3 );
            $customer->setRefCustomer($refCustomer);
            $customer->setRefEmployed($employed);
            $customer->setCustomerChoice($customerChoice);
            $customer->addProperty($property);

            // Ajout en BDD du nouveau client
            $customerRepository->add($customer);

            // liste tous les clients attachés à leur propriété
            $customers = $customerRepository->listbyproperty($property);

            return $this->json([
                'code'=> 200,
                'message' => "Le vendeur a été correctement ajouté.",
                'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                    'customers' => $customers,
                    'idproperty' => $idproperty
                ])
            ], 200);
        }

        //dd('erreur soumission');

        $view = $this->render('gestapp/customer/add.html.twig', [
            'customer' => $customer,
            'form' => $form
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'formulaire présenté',
            'formView' => $view->getContent()
        ]);
    }

    #[Route('/addcustomer/{type}/{option}', name: 'op_gestapp_customer_addcustomer',  methods: ['GET', 'POST'])]
    public function addCustomer(
        Request $request,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository,
        PropertyRepository $propertyRepository,
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        $idproperty
    )
    {
        $user = $this->getUser()->getId();
        $employed = $employedRepository->find($user);
        $property = $propertyRepository->find($idproperty);
        $customerChoice = $customerChoiceRepository->find(1);

        $customer = new Customer();
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_addcustomerjson', [
                'idproperty' => $idproperty
            ]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Contruction de la référence pour chaque propriété
            $date = new \DateTime();
            $refCustomer = $date->format('Y').'/'.$date->format('m').'-'.substr($form->get('firstName')->getData(), 0,3 ).substr($form->get('lastName')->getData(), 0,3 );
            $customer->setRefCustomer($refCustomer);
            $customer->setRefEmployed($employed);
            $customer->setCustomerChoice($customerChoice);
            $customer->addProperty($property);

            // Ajout en BDD du nouveau client
            $customerRepository->add($customer);

            // liste tous les clients attachés à leur propriété
            $customers = $customerRepository->listbyproperty($property);

            return $this->json([
                'code'=> 200,
                'message' => "Le vendeur a été correctement ajouté.",
                'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                    'customers' => $customers,
                    'idproperty' => $idproperty
                ])
            ], 200);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->json([
                'code'=> 200,
                'message' => "Le vendeur a été correctement ajouté."
            ], 200);
        }

        return $this->render('gestapp/customer/add.html.twig', [
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
            // partie ajout CI
            $ci = $form->get('cifilename')->getData();
            $ciFilename = $customer->getCifilename();
            if($ci) {
                if ($ciFilename) {
                    $pathheader = $this->getParameter('customer_ci_directory') . '/' . $ciFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = $customer->getFirstName().'-'.$customer->getLastName().'-ci.' . $ci->guessExtension();
                try {
                    $ci->move(
                        $this->getParameter('customer_ci_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setCifilename($newFilename);
            }

            // partie Ajout Kbis
            $kbis = $form->get('kbisfilename')->getData();
            $kbisFilename = $customer->getKbisfilename();
            if($kbis) {
                if ($kbisFilename) {
                    $pathheader = $this->getParameter('customer_kbis_directory') . '/' . $kbisFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = $customer->getFirstName().'-'.$customer->getLastName().'-kbis.' . $kbis->guessExtension();
                try {
                    $kbis->move(
                        $this->getParameter('customer_kbis_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setKbisfilename($newFilename);
            }
            $customerRepository->add($customer);
            return $this->redirectToRoute('op_gestapp_customer_edit', ['id'=>$customer->getId()], Response::HTTP_SEE_OTHER);
        }

        //dd($form->isSubmitted());

        return $this->render('gestapp/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/getformcustomer/{id}', name: 'op_gestapp_customer_getform', methods: ['GET'])]
    public function getFormCustomer(Customer $customer,Request $request)
    {
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_getform', ['id'=> $customer->getId()]),
            'method'=>'POST',
            'attr' => ['class'=>'formEditCustomer']
        ]);

        $form->handleRequest($request);
        $view = $this->render('gestapp/customer/_form2.html.twig', [
            'customer' => $customer,
            'form' => $form
        ]);
        //dd($view->getContent());
        return $this->json([
            'code'=> 200,
            'form' => $view->getContent()
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
        TransactionRepository $transactionRepository,
        CustomerChoiceRepository $customerChoiceRepository,
        SluggerInterface $slugger
    )
    {
        $form = $this->createForm(Customer2Type::class, $customer, [
            'action'=> $this->generateUrl('op_gestapp_customer_editcustomerjson', [
                'id'=> $customer->getId(),
                'idproperty' => $idproperty
            ]),
            'method'=>'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            $customers = $customerRepository->listbyproperty($idproperty);

            // partie ajout CI
            $ci = $form->get('cifilename')->getData();
            $ciFilename = $customer->getCifilename();
            if($ci) {
                if ($ciFilename) {
                    $pathheader = $this->getParameter('customer_ci_directory') . '/' . $ciFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = $customer->getFirstName().'-'.$customer->getLastName().'-ci.' . $ci->guessExtension();
                try {
                    $ci->move(
                        $this->getParameter('customer_ci_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setCifilename($newFilename);
            }

            // partie Ajout Kbis
            $kbis = $form->get('kbisfilename')->getData();
            $kbisFilename = $customer->getKbisfilename();
            if($kbis) {
                if ($kbisFilename) {
                    $pathheader = $this->getParameter('customer_kbis_directory') . '/' . $kbisFilename;
                    // On vérifie si l'image existe
                    if (file_exists($pathheader)) {
                        unlink($pathheader);
                    }
                }
                $newFilename = $customer->getFirstName().'-'.$customer->getLastName().'-kbis.' . $kbis->guessExtension();
                try {
                    $kbis->move(
                        $this->getParameter('customer_kbis_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $customer->setKbisfilename($newFilename);
            }

            return $this->json([
                'code'=> 200,
                'message' => "Le vendeur a été correctement modifié.",
                'liste' => $this->renderView('gestapp/customer/_listecustomers.html.twig', [
                    'customers' => $customers,
                    'idproperty' => $idproperty
                ])
            ], 200);
        }
        $customers = $customerRepository->listbyproperty($idproperty);
        // Affichage du formulaire de modification du client
        $view = $this->render('gestapp/customer/_form2.html.twig', [
            'customer' => $customer,
            'form' => $form
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'Modifier les informations du Client',
            'formView' => $view->getContent()
        ],200);
    }

    #[Route('/{id}', name: 'op_gestapp_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer);
        }

        return $this->redirectToRoute('op_gestapp_customer_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @param CustomerRepository $customerRepository
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * Suppression d'un client depuis la page index "Client"
     */
    #[Route('/del/{id}', name: 'op_gestapp_customer_del', methods: ['POST'])]
    public function del(Request $request, Customer $customer, CustomerRepository $customerRepository, PropertyRepository $propertyRepository, PaginatorInterface $paginator): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $properties = $customer->getProperties();
        if($properties){
            foreach ($properties as $property){
                $property->removeCustomer($customer);
            }
        }
        $customerRepository->remove($customer);

        if($hasAccess == true){
            // on liste tous les clients quelques soit les utilisateurs
            $data = $customerRepository->findAllCustomer();

            $customers = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
        }else{
            $data = $customerRepository->findAllCustomerByEmployed(['refEmployed' => $user]);
            $customers = $paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->json([
            'code'=> 200,
            'message' => "Le client a été correctement supprimée de l'application.",
            'liste' => $this->renderView('gestapp/customer/_list.html.twig', [
                'customers' => $customers
            ])
        ], 200);
    }

    #[Route('/{id}/delonproperty/{idproperty}', name: 'op_gestapp_customer_del_onproperty', methods: ['POST'])]
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
                'idproperty' => $idproperty,
            ])
        ], 200);

    }

}
