<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use App\DataSet\DataSetColumnList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnStringMapItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $names = $options['data_set_columns']->getNames();
        $builder
            ->add('from', ChoiceType::class, [
                'choices' => array_combine($names, $names),
                'placeholder' => '',
            ])
            ->add('to', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired('data_set_columns')
            ->setAllowedTypes('data_set_columns', DataSetColumnList::class);
    }
}
