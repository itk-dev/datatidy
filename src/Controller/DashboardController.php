<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\DataFlowJobRepository;
use App\Repository\DataFlowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index(DataFlowRepository $dataFlowRepository, DataFlowJobRepository $dataFlowJobRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $recentDataFlows = $dataFlowRepository->findByUser(
            $user,
            ['updatedAt' => 'desc'],
            5
        );

        $recentJobs = $dataFlowJobRepository->findByUser(
            $user,
            ['updatedAt' => 'desc'],
            5
        );

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'recent_data_flows' => $recentDataFlows,
            'recent_jobs' => $recentJobs,
        ]);
    }
}
