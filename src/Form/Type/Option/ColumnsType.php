<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\Option;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnsType extends ChoiceType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options = array_replace($options, [
            'choices' => $this->getChoices($options),
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
        // Make sure that we save a list (i.e. not an associative array with numerical keys).
        return array_values($value);
    }

    private function getChoices(array $options)
    {
        /** @var ArrayCollection $columns */
        $columns = $options['data_set_columns'];
        $names = $columns->getKeys();

        return array_combine($names, $names);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired('data_set_columns')
            ->setAllowedTypes('data_set_columns', ArrayCollection::class)
            ->setDefaults([
                'multiple' => true,
                'expanded' => true,
            ]);
    }
}
