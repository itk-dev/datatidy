<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('columns', ChoiceType::class, [
            'choices' => $this->getChoices($options),
            'multiple' => true,
            'expanded' => true,
            'label' => false,
        ]);

//        parent::buildForm($builder, $options);
    }

    private function getChoices(array $options)
    {
        $names = $options['columns'];

        return array_combine($names, $names);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('columns');
    }
}
