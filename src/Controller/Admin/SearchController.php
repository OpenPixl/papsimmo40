<?php

namespace App\Controller\Admin;

use App\Form\Gestapp\SearchPropertyType;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchPhrasePrefix;
use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    )
    {
    }

    #[Route('/admin/search/property', name: 'app_admin_search_property')]
    public function property(Request $request): Response
    {

        $form = $this->createForm(SearchPropertyType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $page = $request->query->getInt('page', 1);

            $boolQuery = new BoolQuery();
            if ($data->name){
                $boolQuery->addMust(new MatchPhrasePrefix('name', $data->name));
                //dd($boolQuery);
            }

            if ($data->refEmployed){

                $boolQuery->addFilter(new MatchQuery('employed.id', $data->refEmployed->getId()));
                //dd($boolQuery);
            }
            $results = $this->finder->createPaginatorAdapter($boolQuery);
            //dd($results);
            $pagination = $this->paginator->paginate($results, $page);
            dd($pagination);

        }

        return $this->render('admin/search/searchproperty.html.twig', [
            'form' => $form,
            'pagination' => $pagination ?? []
        ]);
    }
}
