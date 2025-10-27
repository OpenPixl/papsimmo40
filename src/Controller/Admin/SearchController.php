<?php

namespace App\Controller\Admin;

use App\Form\Admin\Search\SearchConstructType;
use App\Form\Admin\Search\SearchCustomerPropertyType;
use App\Form\Admin\Search\SearchCustomerType;
use App\Form\Admin\Search\SearchPropertyDashboardType;
use App\Form\Admin\Search\SearchPropertyType;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrasePrefix;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\Query\MultiMatch;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder,
        private FinderInterface $customerFinder
    )
    {
    }

    #[Route('/test/search/construct', name: 'app_admin_search_construct')]
    public function construct(Request $request): Response
    {
        $form = $this->createForm(SearchConstructType::class, null, [
            'action' => $this->generateUrl('app_admin_search_construct')
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $page = $request->query->getInt('page', 1);

            $boolQuery = new BoolQuery();

            if (!empty($data->name)){
                $fieldQuery = new \Elastica\Query\MatchPhrasePrefix();
                $fieldQuery->setField('name', $data->name);
                $boolQuery->addMust($fieldQuery);
            }

            if (!empty($data->refmandat)) {
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('refmandat', $data->refmandat);
                $boolQuery->addMust($termQuery);
            }

            if (!empty($data->zipcode)){
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('zipcode', $data->zipcode);
                $boolQuery->addMust($termQuery);
            }

            if (!empty($data->city)) {
                $fieldQuery = new \Elastica\Query\MatchPhrase();
                $fieldQuery->setField('city', $data->city);
                $boolQuery->addMust($fieldQuery);
            }

            // Filtrer par biens sans mandat si la checkbox est cochée
            if ($data->isNomandat) {
                //dd($data->isNomandat);
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('isNomandat', $data->isNomandat);
                $boolQuery->addMust($termQuery);
            }

            $results = $this->finder->createPaginatorAdapter($boolQuery);
            $properties = $this->paginator->paginate($results, $page);
        }

        return $this->render('admin/search/searchconstruct.html.twig', [
            'form' => $form,
            'properties' => $properties ?? []
        ]);
    }

    #[Route('/admin/search/property/', name: 'app_admin_search_property', methods: ['POST', 'GET'])]
    public function propertyAdmin(Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $form = $this->createForm(SearchPropertyType::class, null, [
            'action' => $this->generateUrl('app_admin_search_property'),
            'method' => 'POST',
            'attr' => [
                'id' => 'SearchFormProperty'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $minPrice = $data->minPrice;
            $maxPrice = $data->maxPrice;
            $page = $request->query->getInt('page', 1);

            $boolQuery = new BoolQuery();

            if (!empty($data->refmandat)) {
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('refmandat', $data->refmandat);
                $boolQuery->addMust($termQuery);
            }

            if (!empty($data->city)){
                $fieldQuery = new \Elastica\Query\MatchPhrase();
                $fieldQuery->setField('city', $data->city);
                $boolQuery->addMust($fieldQuery);
            }

            if (!empty($data->zipcode)){
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('zipcode', $data->zipcode);
                $boolQuery->addMust($termQuery);
            }

            if ($minPrice !== null || $maxPrice !== null) {
                $rangeFilter = [];
                if ($minPrice !== null) {
                    $rangeFilter['gte'] = (float) $minPrice; // 'gte' = greater than or equal
                }
                if ($maxPrice !== null) {
                    $rangeFilter['lte'] = (float) $maxPrice; // 'lte' = less than or equal
                }
                //dd($rangeFilter);
                $boolQuery->addFilter(new Range('price', $rangeFilter));
            }

            $termQuery = new \Elastica\Query\Term();
            $termQuery->setTerm('isArchived', false);
            $boolQuery->addMust($termQuery);

            if($hasAccess == false){
                $boolQuery->addFilter(new Term(['refEmployed.id' => $user->getId()]));;
            }

            $query = new Query($boolQuery);
            $query->setSort([
                'refmandat' => ['order' => 'desc'],
            ]);

            $results = $this->finder->createPaginatorAdapter($query);
            $properties = $this->paginator->paginate($results, $page);

            return $this->json([
                'list' => $this->renderView('gestapp/property/include/_list.html.twig', [
                    'properties' => $properties,
                ]),
            ], 200);
        }

        return $this->render('admin/search/searchproperty.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/search/propertydashboard/', name: 'app_admin_search_propertydashboard', methods: ['POST', 'GET'])]
    public function propertyDashboard(Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();

        $form = $this->createForm(SearchPropertyDashboardType::class, null, [
            'action' => $this->generateUrl('app_admin_search_propertydashboard'),
            'method' => 'POST',
            'attr' => [
                'id' => 'SearchFormProperty'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();
            $minPrice = $data->minPrice;
            $maxPrice = $data->maxPrice;
            $page = $request->query->getInt('page', 1);

            $boolQuery = new BoolQuery();

            if ($minPrice !== null || $maxPrice !== null) {
                $rangeFilter = [];
                if ($minPrice !== null) {
                    $rangeFilter['gte'] = (float) $minPrice; // 'gte' = greater than or equal
                }
                if ($maxPrice !== null) {
                    $rangeFilter['lte'] = (float) $maxPrice; // 'lte' = less than or equal
                }
                $boolQuery->addFilter(new Range('price', $rangeFilter));
            }

            if (!empty($data->projet)){
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('rubric.id', $data->projet);
                $boolQuery->addMust($termQuery);
            }

            if (!empty($data->zipcode)){
                $termQuery = new \Elastica\Query\Term();
                $termQuery->setTerm('zipcode', $data->zipcode);
                $boolQuery->addMust($termQuery);
            }

            if (!empty($data->city)){
                $fieldQuery = new \Elastica\Query\MatchPhrase();
                $fieldQuery->setField('city', $data->city);
                $boolQuery->addMust($fieldQuery);
            }

            $query = new Query($boolQuery);
            $query->setSort([
                'refmandat' => ['order' => 'desc'],
            ]);

            $results = $this->finder->createPaginatorAdapter($query);
            $properties = $this->paginator->paginate($results, $page);

            return $this->json([
                'list' => $this->renderView('gestapp/property/include/_list-dashboard.html.twig', [
                    'properties' => $properties,
                ]),
            ], 200);
        }

        return $this->render('admin/search/searchpropertydashboard.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/search/customeronindex/', name: 'app_admin_search_customeronindex', methods: ['POST', 'GET'])]
    public function customerOnIndex(Request $request, TransformedFinder $customerFinder,): Response
    {

        $form = $this->createForm(SearchCustomerType::class, null, [
            'action' => $this->generateUrl('app_admin_search_customeronindex'),
            'method' => 'POST',
            'attr' => [
                'id' => 'SearchFormCustomer'
            ]
        ]);
        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->slug;
            $page = $request->query->getInt('page', 1);

            //dd($data);

            if (empty($data)) {
                return $this->json([]);
            }

            // Découper la recherche sur les espaces
            $terms = preg_split('/\s+/', $data);

            // Le code ci dessous :
            // - Découpe la chaîne de recherche sur les espaces
            // - Pour chaque mot, construit un sous-bool qui cherche dans firstName ou lastName (should)
            // - Combine tous ces sous-bool avec un must (= tous les mots doivent apparaître au moins une fois dans les deux champs)
            // - Utilise MatchPhrasePrefix pour que ce soit tolérant aux débuts de mots

            $boolQuery = new BoolQuery();

            foreach ($terms as $term) {
                $should = new BoolQuery();
                $should->addShould(new MatchPhrasePrefix('firstName', $term));
                $should->addShould(new MatchPhrasePrefix('lastName', $term));

                $boolQuery->addMust($should);
            }

            $query = new Query($boolQuery);

            // Récupérer les résultats (objets Customer)
            $results = $customerFinder->find($query);


            $customers = $this->paginator->paginate($results, $page);
            //dd($customers);

            return $this->json([
                'liste' => $this->renderView('gestapp/customer/include/_listsearch.html.twig', [
                    'customers' => $customers,
                ]),
            ], 200);
        }

        return $this->render('admin/search/searchcustomerindex.html.twig', [
            'form' => $form,
        ]);
    }

    // outil de Recherche d'un client sur la vue Show Property
    #[Route('/admin/search/customer_property/{idproperty}', name: 'op_admin_search_customer_property', methods: ['POST', 'GET'])]
    public function customerProperty(Request $request, $idproperty, TransformedFinder $customerFinder): Response
    {
        $form = $this->createForm(SearchCustomerPropertyType::class, null, [
            'action' => $this->generateUrl('op_admin_search_customer_property',[
                'idproperty' => $idproperty,
            ]),
            'method' => 'POST',
            'attr' => [
                'id' => 'formProperty_searchCustomer'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData()->firstName;

            if (empty($data)) {
                return $this->json([]);
            }

            // Découper la recherche sur les espaces
            $terms = preg_split('/\s+/', $data);

            // Le code ci dessous :
            // - Découpe la chaîne de recherche sur les espaces
            // - Pour chaque mot, construit un sous-bool qui cherche dans firstName ou lastName (should)
            // - Combine tous ces sous-bool avec un must (= tous les mots doivent apparaître au moins une fois dans les deux champs)
            // - Utilise MatchPhrasePrefix pour que ce soit tolérant aux débuts de mots

            $boolQuery = new BoolQuery();

            foreach ($terms as $term) {
                $should = new BoolQuery();
                $should->addShould(new MatchPhrasePrefix('firstName', $term));
                $should->addShould(new MatchPhrasePrefix('lastName', $term));

                $boolQuery->addMust($should);
            }

            $query = new Query($boolQuery);

            // Récupérer les résultats (objets Customer)
            $customers = $customerFinder->find($query);

            return $this->json([
                'code'=> 200,
                'message' => "La recherche à aboutie",
                'liste' => $this->renderView('gestapp/customer/search/_liste.html.twig', [
                    'customers' => $customers,
                    'idproperty' => $idproperty
                ])
            ]);
        }

        return $this->render('gestapp/customer/search/_listsearch.html.twig', [
            'form' => $form,
            'idproperty' => $idproperty,
        ]);
    }

    // outil de Recherche d'un client sur la vue Show Property
    #[Route('/admin/search/customer_transaction/{idtransaction}', name: 'op_admin_search_customer_transaction', methods: ['POST', 'GET'])]
    public function customerTransaction(Request $request, $idtransaction, TransformedFinder $customerFinder): Response
    {
        $form = $this->createForm(SearchCustomerPropertyType::class, null, [
            'action' => $this->generateUrl('op_admin_search_customer_transaction',[
                'idtransaction' => $idtransaction,
            ]),
            'method' => 'POST',
            'attr' => [
                'id' => 'formSearch_CustomerTransaction'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData()->firstName;

            if (empty($data)) {
                return $this->json([]);
            }

            // Découper la recherche sur les espaces
            $terms = preg_split('/\s+/', $data);

            // Le code ci dessous :
            // - Découpe la chaîne de recherche sur les espaces
            // - Pour chaque mot, construit un sous-bool qui cherche dans firstName ou lastName (should)
            // - Combine tous ces sous-bool avec un must (= tous les mots doivent apparaître au moins une fois dans les deux champs)
            // - Utilise MatchPhrasePrefix pour que ce soit tolérant aux débuts de mots

            $boolQuery = new BoolQuery();

            foreach ($terms as $term) {
                $should = new BoolQuery();
                $should->addShould(new MatchPhrasePrefix('firstName', $term));
                $should->addShould(new MatchPhrasePrefix('lastName', $term));

                $boolQuery->addMust($should);
            }

            $query = new Query($boolQuery);

            // Récupérer les résultats (objets Customer)
            $customers = $customerFinder->find($query);

            return $this->json([
                'code'=> 200,
                'message' => "La recherche à aboutie",
                'liste' => $this->renderView('gestapp/customer/search/_liste.html.twig', [
                    'customers' => $customers,
                    'idtransaction' => $idtransaction
                ])
            ]);
        }

        return $this->json([
            'formView' => $this->renderView('gestapp/customer/search/_listsearch.html.twig', [
                'form' => $form,
                'idtransaction' => $idtransaction,
            ])
        ],200);
    }
}
