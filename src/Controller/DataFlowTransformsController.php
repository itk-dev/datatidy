<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\DataFlow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data/flow/{data_flow}/transforms", name="data_flow_transforms_")
 * @ParamConverter("dataFlow", class="App\Entity\DataFlow", options={"id"="data_flow"})
 */
class DataFlowTransformsController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DataFlow $dataFlow): Response
    {
        return $this->render('data_flow/transforms/index.html.twig', [
        ]);
    }

    /**
     * @Route("/preview", name="preview", methods={"GET"})
     */
    public function preview(DataFlow $dataFlow)
    {
        return $this->render('data_flow/transforms/preview.html.twig', [
            'data_flow' => $dataFlow,
        ]);
    }
}
