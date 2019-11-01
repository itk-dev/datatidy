<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractDataSourceType extends AbstractType
{
    protected function getBaseFormBuilder(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('url')
            ->add('ttl')
        ;

        return $builder;
    }
}
