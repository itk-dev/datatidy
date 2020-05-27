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
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        DataFlowManager $dataFlowManager,
        DataTransformerManager $dataTransformerManager,
        TranslatorInterface $translator,
        SerializerInterface $serializer
    ) {
        $this->dataFlowManager = $dataFlowManager;
        $this->dataTransformerManager = $dataTransformerManager;
        $this->translator = $translator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/.{_format}", name="index", methods={"GET"},
     *     format="html",
     *     requirements={
     *         "_format": "csv|json",
     *     }
     * )
     */
    public function index(Request $request, DataFlow $dataFlow, string $_format = 'html'): Response
    {
        return $this->show($request, $dataFlow, null, $_format);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, DataFlow $dataFlow)
    {
        $transformer = $request->get('transformer');
        $transform = (new DataTransform())->setDataFlow($dataFlow);
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
     * @Route("/{id}.{_format}", name="show", methods={"GET"},
     *     format="html",
     *     requirements={
     *         "_format": "csv|json",
     *     }
     * )
     */
    public function show(Request $request, DataFlow $dataFlow, DataTransform $transform = null, string $_format = 'html'): Response
    {
        $result = $this->dataFlowManager->run($dataFlow, [
            'number_of_steps' => null !== $transform ? $transform->getPosition() + 1 : 0,
        ]);

        if ($result->isSuccess() && \in_array($_format, ['csv', 'json'], true)) {
            $data = $result->getLastTransformResult()->getRows();
            if ($request->get('as_object')) {
                $data = reset($data);
            }
            $content = $this->serializer->serialize($data, $_format);
            $contentType = [
                'csv' => 'text/csv',
                'json' => 'application/json',
            ][$_format] ?? 'text/plain';

            return new Response($content, Response::HTTP_OK, ['content-type' => $contentType]);
        }

        return $this->render('data_flow/transforms/show.html.twig', [
            'data_flow' => $dataFlow,
            'transform' => $transform,
            'result' => $result,
            'transformers' => $this->dataTransformerManager->getTransformers(),
        ]);
    }

    /**
     * @Route("/preview/{id}", name="preview", methods={"GET"}, defaults={"id": null})
     */
    public function preview(Request $request, DataFlow $dataFlow, DataTransform $transform = null)
    {
        $totalNumberOfSteps = $dataFlow->getTransforms()->count();
        $numberOfSteps = null !== $transform ? $transform->getPosition() + 1 : 0;

        $result = $this->dataFlowManager->run($dataFlow, [
            'number_of_steps' => $numberOfSteps,
        ]);

        return $this->render('data_flow/transforms/preview.html.twig', [
            'data_flow' => $dataFlow,
            'result' => $result,
            'number_of_steps' => $numberOfSteps,
            'total_steps' => $totalNumberOfSteps,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, DataFlow $dataFlow, DataTransform $transform)
    {
        if ($transform->getDataFlow() !== $dataFlow) {
            throw new BadRequestHttpException();
        }

        $options = [];
        if (null !== $transform->getId()) {
            $options['number_of_steps'] = $transform->getPosition();
        }
        $result = $this->dataFlowManager->run($dataFlow, $options);

        $form = $this->createForm(DataTransformType::class, $transform, [
            'data_set_columns' => $result->isSuccess() ? $result->getTransformResult(-1)->getColumns() : new ArrayCollection(),
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

            return $this->redirectToRoute('data_flow_transforms_show', ['data_flow' => $dataFlow->getId(), 'id' => $transform->getId()]);
        }

        $previousTransform = 0 < $transform->getPosition()
            ? $dataFlow->getTransforms()[$transform->getPosition() - 1]
            : null;

        $parameters = [
            'data_flow' => $dataFlow->getId(),
            'id' => $transform->getId() ?? 'show',
        ];

        $cancelUrl = $this->generateUrl('data_flow_transforms_show', $parameters);

        return $this->render('data_flow/transforms/edit.html.twig', [
            'data_flow' => $dataFlow,
            'transform' => $transform,
            'previous_transform' => $previousTransform,
            'result' => $result,
            'form' => $form->createView(),
            'cancel_url' => $cancelUrl,
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
