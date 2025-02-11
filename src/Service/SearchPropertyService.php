<?php

namespace App\Service;


use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Range;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchPropertyService
{
    public function __construct(
        protected Request $request,
        private readonly PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    )
    {}

    public function dashboard_search_property($form)
    {
        $data = $form->getData();
        $minPrice = $data->minPrice;
        $maxPrice = $data->maxPrice;
        $page = $this->request->query->getInt('page', 1);

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

        return $properties;
    }
}