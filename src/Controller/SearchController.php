<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Repository\DataFlowJobRepository;
use App\Repository\DataFlowRepository;
use App\Repository\DataSourceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    private $dataFlowRepository;
    private $dataFlowJobRepository;
    private $dataSourceRepository;

    public function __construct(DataFlowRepository $dataFlowRepository, DataFlowJobRepository $dataFlowJobRepository, DataSourceRepository $dataSourceRepository)
    {
        $this->dataFlowRepository = $dataFlowRepository;
        $this->dataFlowJobRepository = $dataFlowJobRepository;
        $this->dataSourceRepository = $dataSourceRepository;
    }

    /**
     * @Route("/", name="search_index", methods={"GET"})
     */
    public function index(Request $request)
    {
        $query = $request->query->get('query', '');

        if (!empty($query)) {
            $matchedDataFlows = $this->searchDataFlows($query);
            $matchedDataSources = $this->searchDataSources($query);
        } else {
            $matchedDataFlows = $this->dataFlowRepository->findAll();
            $matchedDataSources = $this->dataSourceRepository->findAll();
        }

        return $this->render('search/index.html.twig', [
            'data_flows' => $matchedDataFlows,
            'data_sources' => $matchedDataSources,
            'number_of_results' => count($matchedDataFlows) + count($matchedDataSources),
        ]);
    }

    private function searchDataFlows(string $search)
    {
        $queryBuilder = $this->dataFlowRepository->createQueryBuilder('e');

        $queryBuilder->where('e.name LIKE :search');
        $queryBuilder->setParameter(':search', $search);

        return $queryBuilder->getQuery()->getResult();
    }

    private function searchDataSources(string $search)
    {
        $queryBuilder = $this->dataSourceRepository->createQueryBuilder('e');

        $queryBuilder->where('e.name LIKE :search');
        $queryBuilder->setParameter(':search', $search);

        return $queryBuilder->getQuery()->getResult();
    }
}
