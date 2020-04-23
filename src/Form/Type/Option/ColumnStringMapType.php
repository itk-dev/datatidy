<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use App\DataSet\DataSetColumnList;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnStringMapType extends CollectionType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options = array_replace($options, [
            'entry_type' => ColumnStringMapItemType::class,
            'entry_options' => [
                'data_set_columns' => $options['data_set_columns'],
                'label' => false,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'attr' => [
                'data-button-add-text' => 'Add another mapping',
            ],
        ]);

        $builder->addModelTransformer($this);
        parent::buildForm($builder, $options);
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        // Re-index array from 0.
        if (\is_array($value)) {
            $value = array_values($value);
        }

        return $value;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired('data_set_columns')
            ->setAllowedTypes('data_set_columns', DataSetColumnList::class);
    }

    public function getBlockPrefix()
    {
        return 'column_string_map';
    }
}
