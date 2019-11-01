<?php

namespace App\Form;

use App\Entity\AbstractDataSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
