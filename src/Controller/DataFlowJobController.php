<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\DataFlowJob;
use App\Repository\DataFlowJobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data/flow2/job")
 */
class DataFlowJobController extends AbstractController
{
    /**
     * @Route("/", name="data_flow_job_index", methods={"GET"})
     */
    public function index(DataFlowJobRepository $dataFlowJobRepository): Response
    {
        return $this->render('data_flow_job/index.html.twig', [
            'data_flow_jobs' => $dataFlowJobRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="data_flow_job_show", methods={"GET"})
     */
    public function show(DataFlowJob $dataFlowJob): Response
    {
        return $this->render('data_flow_job/show.html.twig', [
            'data_flow_job' => $dataFlowJob,
        ]);
    }
}
