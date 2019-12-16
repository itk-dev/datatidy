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
use App\Entity\User;
use App\Repository\DataFlowJobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(DataFlowJobRepository $dataFlowJobRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $jobs = $dataFlowJobRepository->findByUser(
            $user,
            ['createdAt' => 'desc']
        );

        return $this->render('data_flow_job/index.html.twig', [
            'data_flow_jobs' => $jobs,
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
