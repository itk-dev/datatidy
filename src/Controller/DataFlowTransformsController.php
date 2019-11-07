<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\DataFlow\DataFlowManager;
use App\DataTransformer\DataTransformerManager;
use App\DataTransformer\Exception\InvalidTransformerException;
use App\Entity\AbstractDataTransform;
use App\Entity\DataFlow;
use App\Entity\DataTransform;
use App\Form\Type\DataTransformType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data/flow/{data_flow}/transforms", name="data_flow_transforms_")
 * @ParamConverter("dataFlow", class="App\Entity\DataFlow", options={"id"="data_flow"})
 */
class DataFlowTransformsController extends AbstractController
{
    /** @var DataFlowManager */
    private $manager;

    public function __construct(DataFlowManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DataFlow $dataFlow, DataTransformerManager $transformerManager): Response
    {
//        $transform = new AbstractDataTransform();
//        $form = $this->createForm(DataTransformType::class, $transform);

        return $this->render('data_flow/transforms/index.html.twig', [
            'data_flow' => $dataFlow,
//            'transform_form' => $form->createView(),
            'transformers' => $transformerManager->getTransformers(),
        ]);
    }

    /**
     * @Route("/preview", name="preview", methods={"GET"})
     */
    public function preview(Request $request, DataFlow $dataFlow)
    {
        $totalNumberOfSteps = $dataFlow->getTransforms()->count();
        $steps = $request->get('steps');
        if (null === $steps) {
            $steps = $totalNumberOfSteps;
        }
        $results = $this->manager->run($dataFlow, [
            'steps' => $steps,
            'return_exceptions' => true,
        ]);

        return $this->render('data_flow/transforms/preview.html.twig', [
            'data_flow' => $dataFlow,
            'results' => $results,
            'step' => $steps,
            'total_steps' => $totalNumberOfSteps,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, DataFlow $dataFlow, DataTransformerManager $transformerManager)
    {
        $transformer = $request->get('transformer');
        $transform = new DataTransform();
        if (null !== $transformer) {
            try {
                $transformerManager->getTransformer($transformer);
                $transform->setTransformer($transformer);
            } catch (InvalidTransformerException $invalidTransformerException) {
                $this->addFlash('danger', 'Invalid transformer');

                return $this->redirectToRoute('data_flow_transforms_index', ['data_flow' => $dataFlow->getId()]);
            }
        }

        $form = $this->createForm(DataTransformType::class, $transform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dataFlow);
            $entityManager->flush();

//            return $this->redirectToRoute('data_flow_transforms_index', ['data_flow' => $dataFlow->getId()]);
        }

        return $this->render('data_flow/transforms/edit.html.twig', [
            'data_flow' => $dataFlow,
            'transform' => $transform,
            'form' => $form->createView(),
        ]);
    }
}
