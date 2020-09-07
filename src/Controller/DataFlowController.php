<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\DataFlow\DataFlowManager;
use App\Entity\DataFlow;
use App\Filter\DataFlowFilterType;
use App\Form\Type\DataFlowCreateType;
use App\Form\Type\DataFlowType;
use App\ReferenceManager\DataFlowReferenceManager;
use App\Repository\DataFlowRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/data/flow")
 */
class DataFlowController extends AbstractController
{
    /**
     * @Route("/", name="data_flow_index", methods={"GET"})
     */
    public function index(
        Request $request,
        DataFlowRepository $dataFlowRepository,
        FilterBuilderUpdaterInterface $filterBuilderUpdater
    ): Response {
        $filterForm = $this->createForm(DataFlowFilterType::class);

        $dataFlows = null;

        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));

            $filterBuilder = $dataFlowRepository->createQueryBuilder('e');
            $filterBuilderUpdater->addFilterConditions($filterForm, $filterBuilder);

            $dataFlows = $filterBuilder->getQuery()->getResult();
        } else {
            $dataFlows = $dataFlowRepository->findAll();
        }

        return $this->render('data_flow/index.html.twig', [
            'data_flows' => $dataFlows,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/new", name="data_flow_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $dataFlow = new DataFlow();
        $form = $this->createForm(DataFlowCreateType::class, $dataFlow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dataFlow);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Data flow %name% created', [
                '%name%' => $dataFlow->getName(),
            ]));

            return $this->redirectToRoute('data_flow_edit', ['id' => $dataFlow->getId()]);
        }

        return $this->render('data_flow/new.html.twig', [
            'form' => $form->createView(),
            'cancel_url' => $this->generateUrl('data_flow_index'),
        ]);
    }

    /**
     * @Route("/{id}", name="data_flow_show", methods={"GET"})
     */
    public function show(DataFlow $dataFlow): Response
    {
        return $this->render('data_flow/show.html.twig', [
            'data_flow' => $dataFlow,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="data_flow_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DataFlow $dataFlow, TranslatorInterface $translator): Response
    {
        $scheduleHelp = null !== $dataFlow->getSchedule()
            ? $translator->trans(
                'Next at %time%',
                ['%time%' => $dataFlow->getSchedule()->getNextRunDate()->format('d-m-Y H:i:s')]
            )
            : '';

        $form = $this->createForm(DataFlowType::class, $dataFlow, ['schedule_help' => $scheduleHelp]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // @TODO Why is this needed to connect new targets to the flow?
            foreach ($dataFlow->getDataTargets() as $target) {
                $target->setDataFlow($dataFlow);
            }
            // @TODO: This should be handled more elegantly.
            $dataFlow->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->persist($dataFlow);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('data_flow_edit', ['id' => $dataFlow->getId()]);
        }

        return $this->render('data_flow/edit.html.twig', [
            'data_flow' => $dataFlow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="data_flow_delete", methods={"GET", "DELETE"})
     */
    public function delete(
        Request $request,
        DataFlow $dataFlow,
        DataFlowReferenceManager $referenceManager,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createFormBuilder($dataFlow)
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $referenceManager->delete($dataFlow);
                $this->addFlash(
                    'success',
                    $translator->trans('Data flow %data_flow% deleted', [
                        '%data_flow%' => $dataFlow->getName(),
                    ])
                );
            } catch (Exception $exception) {
                $this->addFlash(
                    'danger',
                    $translator->trans('Error deleting %data_flow%: %message%', [
                        '%data_flow%' => $dataFlow->getName(),
                        '%message%' => $exception->getMessage(),
                    ])
                );
            }

            return $this->redirectToRoute('data_flow_index');
        }

        $parameters = [
            'data_flow' => $dataFlow,
        ];

        $messages = $referenceManager->getDeleteMessages($dataFlow);

        if (!empty($messages)) {
            $parameters['messages'] = $messages;
        } else {
            $parameters['form'] = $form->createView();
        }

        return $this->render('data_flow/delete.html.twig', $parameters);
    }

    /**
     * @Route("/{id}/run", name="data_flow_run", methods={"POST"})
     */
    public function run(
        Request $request,
        DataFlow $dataFlow,
        DataFlowManager $dataFlowManager,
        TranslatorInterface $translator
    ): Response {
        $publish = $request->get('publish', false);
        $result = $dataFlowManager->run($dataFlow, [
            'publish' => $publish,
        ]);
        if ($result->isSuccess()) {
            $this->addFlash('success', $translator->trans('Data flow run successfully', [], 'data_flow'));
            if ($publish) {
                if ($result->isPublished()) {
                    $this->addFlash(
                        'success',
                        $translator->trans('Data flow result successfully published', [], 'data_flow')
                    );
                } else {
                    $exception = $result->getPublishException();
                    $this->addFlash(
                        'danger',
                        $exception
                            ? $translator->trans(
                                'Error publishing data flow result (%message%)',
                                ['%message%' => $exception->getMessage()],
                                'data_flow'
                            )
                            : $translator->trans('Error publishing data flow result', [], 'data_flow')
                    );
                }
            }
        } else {
            $this->addFlash('danger', $translator->trans('Error running data flow', [], 'data_flow'));
        }

        $url = $request->get('referer') ?? $this->redirectToRoute('data_flow_edit', ['id' => $dataFlow->getId()]);

        return $this->redirect($url);
    }

    /**
     * @Route("/{id}/collaborator/search", name="data_flow_collaborator_search", methods={"GET"})
     */
    public function collaboratorSearch(
        Request $request,
        DataFlow $dataFlow,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $collaborators = $dataFlow->getCollaborators();
        $collaborators->add($dataFlow->getCreatedBy());

        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->select(['u.id', 'u.email AS text'])
            ->where('u.username = :query')
            ->setParameter('query', $request->get('q'));
        if (!$collaborators->isEmpty()) {
            $queryBuilder->andWhere('u.id NOT IN (:collaborators)')
                ->setParameter('collaborators', $dataFlow->getCollaborators());
        }
        $nonCollaborators = $queryBuilder
            ->getQuery()
            ->execute();

        $data = ['results' => $nonCollaborators];

        return new JsonResponse(
            $serializer->serialize($data, 'json', ['groups' => ['collaborator']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
