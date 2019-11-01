<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Request\ParamConverter;

use App\Entity\AbstractDataSource;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataSourceToFormParamConverter implements ParamConverterInterface
{
    private $entityManager;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $dataSource = $this->entityManager
            ->getRepository(AbstractDataSource::class)
            ->find(
                $request->attributes->get('id')
            );

        if (empty($dataSource)) {
            throw new NotFoundHttpException();
        }

        $discriminator = $this->entityManager->getClassMetadata(\get_class($dataSource))->discriminatorValue;

        $dataSourceTypeClass = sprintf('App\Form\%sDataSourceType', ucfirst($discriminator));

        if (!class_exists($dataSourceTypeClass)) {
            throw new NotFoundHttpException();
        }

        $request->attributes->set(
            $configuration->getName(),
            $this->formFactory->create($dataSourceTypeClass, $dataSource)
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return 'data_source_to_form' === $configuration->getConverter();
    }
}
