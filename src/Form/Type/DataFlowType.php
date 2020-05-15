<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type;

use App\Entity\DataFlow;
use App\Entity\DataSource;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataFlowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('settings', FormType::class, [
                    'inherit_data' => true,
                ])
                    ->add('name', TextType::class)
                    // @TODO: Use AJAX to load the list of users.
                    ->add('collaborators', EntityType::class, [
                        'class' => User::class,
                        'multiple' => true,
                        'expanded' => true,
                    ])
                    ->add('frequency', IntegerType::class)
            )
            ->add(
                $builder->create('data_source', FormType::class, [
                        'inherit_data' => true,
                ])
                    ->add('dataSource', EntityType::class, [
                        'class' => DataSource::class,
                        'placeholder' => '',
                    ])
            )
            ->add(
                $builder->create('data_targets', FormType::class, [
                        'inherit_data' => true,
                ])
                    ->add('dataTargets', CollectionType::class, [
                        'entry_type' => DataTargetType::class,
                        'entry_options' => [
                            'label' => false,
                            'block_prefix' => 'data_flow_target_item',
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'block_prefix' => 'data_flow_target',
                    ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataFlow::class,
        ]);
    }
}
