<?php

namespace App\Controller;

use App\Entity\CsvDataSource;
use App\Entity\AbstractDataSource;
use App\Entity\JsonDataSource;
use App\Form\CsvDataSourceType;
use App\Form\JsonDataSourceType;
use App\Repository\DataSourceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/data/source")
 */
class DataSourceController extends AbstractController
{
    /**
     * @Route("/", name="data_source_index", methods={"GET"})
     */
    public function index(DataSourceRepository $dataSourceRepository): Response
    {
        $discriminatorMap = $this->getDoctrine()
                                    ->getManager()
                                    ->getClassMetadata(AbstractDataSource::class)
                                    ->discriminatorMap;

        return $this->render('data_source/index.html.twig', [
            'availableFormats' => \array_keys($discriminatorMap),
            'data_sources' => $dataSourceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{format}", name="data_source_new", methods={"GET","POST"})
     * @ParamConverter("form", converter="format_to_form")
     */
    public function new(Request $request, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('data_source_index');
        }

        return $this->render('data_source/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="data_source_show", methods={"GET"})
     */
    public function show(AbstractDataSource $dataSource): Response
    {
        return $this->render('data_source/show.html.twig', [
            'data_source' => $dataSource,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="data_source_edit", methods={"GET","POST"})
     * @ParamConverter("form", converter="data_source_to_form")
     */
    public function edit(Request $request, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('data_source_index');
        }

        return $this->render('data_source/edit.html.twig', [
            'data_source' => $form->getData(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="data_source_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AbstractDataSource $dataSource): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dataSource->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dataSource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('data_source_index');
    }
}
