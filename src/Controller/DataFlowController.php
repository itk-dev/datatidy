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
use App\Form\Type\DataFlowType;
use App\Repository\DataFlowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data/flow")
 */
class DataFlowController extends AbstractController
{
    /**
     * @Route("/", name="data_flow_index", methods={"GET"})
     */
    public function index(DataFlowRepository $dataFlowRepository): Response
    {
        return $this->render('data_flow/index.html.twig', [
            'data_flows' => $dataFlowRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="data_flow_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dataFlow = new DataFlow();
        $form = $this->createForm(DataFlowType::class, $dataFlow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dataFlow);
            $entityManager->flush();

            return $this->redirectToRoute('data_flow_index');
        }

        return $this->render('data_flow/new.html.twig', [
            'form' => $form->createView(),
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
    public function edit(Request $request, DataFlow $dataFlow): Response
    {
        $form = $this->createForm(DataFlowType::class, $dataFlow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($dataFlow);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('data_flow_index');
        }

        return $this->render('data_flow/edit.html.twig', [
            'data_flow' => $dataFlow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="data_flow_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DataFlow $dataFlow): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dataFlow->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dataFlow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('data_flow_index');
    }
}