<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\DataFlowJob;
use App\Entity\User;
use App\Repository\DataFlowJobRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/job", name="data_flow_job_")
 */
class DataFlowJobController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request, DataFlowJobRepository $dataFlowJobRepository, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $query = $dataFlowJobRepository->getByUserQuery($user, 'e');

        $paginator = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            50, /*limit per page*/
            [
                'defaultSortFieldName' => 'e.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        return $this->render('data_flow_job/index.html.twig', [
            'data_flow_jobs' => $paginator,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(DataFlowJob $dataFlowJob): Response
    {
        return $this->render('data_flow_job/show.html.twig', [
            'data_flow_job' => $dataFlowJob,
        ]);
    }
}
