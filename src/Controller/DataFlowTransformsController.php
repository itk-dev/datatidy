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
use App\Entity\DataFlow;
use App\Entity\DataTransform;
use App\Form\Type\DataTransformType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/data/flow/{data_flow}/transforms", name="data_flow_transforms_")
 * @ParamConverter("dataFlow", class="App\Entity\DataFlow", options={"id"="data_flow"})
 */
class DataFlowTransformsController extends AbstractController
{
    /** @var DataFlowManager */
    private $dataFlowManager;

    /** @var DataTransformerManager */
    private $dataTransformerManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        DataFlowManager $dataFlowManager,
        DataTransformerManager $dataTransformerManager,
        TranslatorInterface $translator
    ) {
        $this->dataFlowManager = $dataFlowManager;
        $this->dataTransformerManager = $dataTransformerManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DataFlow $dataFlow): Response
    {
        return $this->render('data_flow/transforms/index.html.twig', [
            'data_flow' => $dataFlow,
            'transformers' => $this->dataTransformerManager->getTransformers(),
        ]);
    }

    /**
     * @Route("/preview/{id}", name="preview", methods={"GET"}, defaults={"id": null})
     */
    public function preview(Request $request, DataFlow $dataFlow, DataTransform $transform = null)
    {
        $totalNumberOfSteps = $dataFlow->getTransforms()->count();
        $steps = null !== $transform ? $transform->getPosition() + 1 : 0;

        $results = $this->dataFlowManager->run($dataFlow, [
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
    public function new(Request $request, DataFlow $dataFlow)
    {
        $transformer = $request->get('transformer');
        $transform = new DataTransform();
        if (null !== $transformer) {
            try {
                $this->dataTransformerManager->getTransformer($transformer);
                $transform->setTransformer($transformer);
            } catch (InvalidTransformerException $invalidTransformerException) {
                $this->addFlash('danger', $this->translator->trans('Invalid transformer'));

                return $this->redirectToRoute('data_flow_transforms_index', ['data_flow' => $dataFlow->getId()]);
            }
        }

        return $this->edit($request, $dataFlow, $transform);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, DataFlow $dataFlow, DataTransform $transform)
    {
        $totalNumberOfSteps = $dataFlow->getTransforms()->count();
        $steps = null !== $transform ? $transform->getPosition() : 0;

        $columns = $this->dataFlowManager->runColumns($dataFlow, [
            'steps' => $steps,
            'return_exceptions' => true,
        ]);

        $form = $this->createForm(DataTransformType::class, $transform, [
            'data_set_columns' => $columns,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isNew = false;
            $entityManager = $this->getDoctrine()->getManager();
            if (null === $transform->getId()) {
                $dataFlow->addTransform($transform);
                $isNew = true;
            }
            $entityManager->persist($dataFlow);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $isNew ? $this->translator->trans('New transform added') : $this->translator->trans('transform updated')
            );

            return $this->redirectToRoute('data_flow_transforms_index', ['data_flow' => $dataFlow->getId()]);
        }

        return $this->render('data_flow/transforms/edit.html.twig', [
            'data_flow' => $dataFlow,
            'transform' => $transform,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     */
    public function delete(Request $request, DataFlow $dataFlow, DataTransform $transform)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($transform);
        $entityManager->flush();

        $this->addFlash('success', $this->translator->trans('Transform deleted'));

        return $this->index($dataFlow);
    }
}