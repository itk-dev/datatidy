<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class StringStringMapType extends CollectionType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options = array_replace($options, [
            'entry_type' => StringStringMapItemType::class,
            'entry_options' => [
                'label' => false,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'attr' => [
                'data-button-add-text' => 'Add another pair',
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

    public function getBlockPrefix()
    {
        return 'string_string_map';
    }
}
